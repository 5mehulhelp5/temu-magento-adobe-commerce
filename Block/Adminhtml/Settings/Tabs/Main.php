<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Settings\Tabs;

use M2E\Temu\Model\Settings;

class Main extends \M2E\Temu\Block\Adminhtml\Magento\Form\AbstractForm
{
    protected \M2E\Core\Helper\Magento\Attribute $magentoAttributeHelper;
    private Settings $settings;

    public function __construct(
        \M2E\Core\Helper\Magento\Attribute $magentoAttributeHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        Settings $settings,
        array $data = []
    ) {
        $this->magentoAttributeHelper = $magentoAttributeHelper;
        $this->settings = $settings;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();

        $configurationHelper = $this->settings;

        $textAttributes = $this->magentoAttributeHelper->filterByInputTypes(
            $this->magentoAttributeHelper->getAll(),
            ['text', 'select', 'weight']
        );

        //region Product Identifier
        $fieldset = $form->addFieldset(
            'product_settings_fieldset',
            [
                'legend' => __('Product'),
                'collapsable' => false,
            ]
        );

        $fieldset->addField(
            'identifier_code_custom_attribute',
            'hidden',
            [
                'name' => 'identifier_code_custom_attribute',
                'value' => $configurationHelper->getIdentifierCodeValue(),
            ]
        );

        $preparedAttributes = [];

        $warningToolTip = '';

        if (
            $configurationHelper->isIdentifierCodeConfigured()
            && !$this->magentoAttributeHelper->isExistInAttributesArray(
                $configurationHelper->getIdentifierCodeValue(),
                $textAttributes
            ) && $this->getData('identifier_code_custom_attribute') != ''
        ) {
            $warningText = __(
                'Selected Magento Attribute is invalid. Please ensure that the Attribute ' .
                'exists in your Magento, has a relevant Input Type and it is included in all Attribute Sets. ' .
                'Otherwise, select a different Attribute from the drop-down.'
            );

            $warningToolTip = __(
                <<<HTML
<span class="fix-magento-tooltip m2e-tooltip-grid-warning">
    {$this->getTooltipHtml((string)$warningText)}
</span>
HTML
            );

            $attrs = ['attribute_code' => $configurationHelper->getIdentifierCodeValue()];
            $attrs['selected'] = 'selected';
            $preparedAttributes[] = [
                'attrs' => $attrs,
                'value' => \M2E\Temu\Model\Settings::VALUE_MODE_ATTRIBUTE,
                'label' => $this->magentoAttributeHelper
                    ->getAttributeLabel($configurationHelper->getIdentifierCodeValue()),
            ];
        }

        foreach ($textAttributes as $attribute) {
            $attrs = ['attribute_code' => $attribute['code']];

            if (
                $configurationHelper->isIdentifierCodeConfigured()
                && $attribute['code'] === $configurationHelper->getIdentifierCodeValue()
            ) {
                $attrs['selected'] = 'selected';
            }
            $preparedAttributes[] = [
                'attrs' => $attrs,
                'value' => \M2E\Temu\Model\Settings::VALUE_MODE_ATTRIBUTE,
                'label' => $attribute['label'],
            ];
        }

        $fieldset->addField(
            'identifier_code_mode',
            self::SELECT,
            [
                'name' => 'identifier_code_mode',
                'label' => __('UPC/EAN'),
                'class' => 'temu-identifier-code validator-required-when-visible',
                'values' => [
                    \M2E\Temu\Model\Settings::VALUE_MODE_NOT_SET => __('Not Set'),
                    [
                        'label' => __('Magento Attributes'),
                        'value' => $preparedAttributes,
                        'attrs' => [
                            'is_magento_attribute' => true,
                        ],
                    ],
                ],
                'value' => !$configurationHelper->isIdentifierCodeConfigured()
                    ? $configurationHelper->getIdentifierCodeMode()
                    : '',
                'create_magento_attribute' => true,
                'tooltip' => __(
                    '%channel_title uses EAN/UPC to associate your Item with its catalog. ' .
                    'Select Attribute where the Product ID values are stored.',
                    [
                        'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                    ]
                ),
                'after_element_html' => $warningToolTip,
                'required' => true,
            ]
        )->addCustomAttribute('allowed_attribute_types', 'text');
        //endregion

        $fieldset = $form->addFieldset(
            'package_settings_fieldset',
            [
                'legend' => __('Package'),
                'collapsable' => false,
            ]
        );

        $this->addPackageDimensionField(Settings::DIMENSION_TYPE_WEIGHT, $fieldset, $textAttributes);
        $this->addPackageDimensionField(Settings::DIMENSION_TYPE_LENGTH, $fieldset, $textAttributes);
        $this->addPackageDimensionField(Settings::DIMENSION_TYPE_WIDTH, $fieldset, $textAttributes);
        $this->addPackageDimensionField(Settings::DIMENSION_TYPE_HEIGHT, $fieldset, $textAttributes);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    private function addPackageDimensionField(
        string $type,
        \Magento\Framework\Data\Form\Element\Fieldset $fieldset,
        array $textAttributes
    ): void {
        $mode = $this->settings->getPackageDimensionMode($type);

        $this->addHiddenInputForCustomAttributeValue($fieldset, $type);
        [$preparedAttributes, $warningToolTip] = $this->prepareAttributesAndWarningTooltip($type, $textAttributes);

        $fieldset->addField(
            "package_{$type}_mode",
            self::SELECT,
            [
                'name' => "package_{$type}_mode",
                'label' => $this->getPackageDimensionLabel($type),
                'class' => "validator-required-when-visible",
                'values' => [
                    Settings::PACKAGE_MODE_NOT_SET => __('Not Set'),
                    Settings::PACKAGE_MODE_CUSTOM_VALUE => __('Custom Value'),
                    [
                        'label' => __('Magento Attributes'),
                        'value' => $preparedAttributes,
                        'attrs' => [
                            'is_magento_attribute' => true,
                        ],
                    ],
                ],
                'value' => $mode !== Settings::PACKAGE_MODE_CUSTOM_ATTRIBUTE ? $mode : '',
                'create_magento_attribute' => true,
                'tooltip' => $this->getPackageDimensionTooltipText($type),
                'after_element_html' => $this->getCustomValueInputHtml($type) . $warningToolTip,
                'required' => true,
            ]
        )->addCustomAttribute('allowed_attribute_types', 'text');
    }

    private function addHiddenInputForCustomAttributeValue(
        \Magento\Framework\Data\Form\Element\Fieldset $fieldset,
        string $type
    ): void {
        $fieldset->addField(
            "package_{$type}_custom_attribute",
            'hidden',
            [
                'name' => "package_{$type}_custom_attribute",
                'value' => $this->settings->getPackageDimensionCustomAttribute($type),
            ]
        );
    }

    private function prepareAttributesAndWarningTooltip(string $type, array $textAttributes): array
    {
        $mode = $this->settings->getPackageDimensionMode($type);
        $customAttribute = $this->settings->getPackageDimensionCustomAttribute($type);

        $preparedAttributes = [];
        $warningToolTip = '';
        if (
            $mode === Settings::PACKAGE_MODE_CUSTOM_ATTRIBUTE
            && !$this->magentoAttributeHelper->isExistInAttributesArray(
                $customAttribute,
                $textAttributes
            )
            && $this->getData("package_{$type}_custom_attribute") != ''
        ) {
            $warningText = __("Selected Magento Attribute is invalid. Please ensure that the " .
                "Attribute exists in your Magento, has a relevant Input Type and it is included in all Attribute Sets. " .
                "Otherwise, select a different Attribute from the drop-down.");

            $warningToolTip = __(
                <<<HTML
<span class="fix-magento-tooltip m2e-tooltip-grid-warning">
    {$this->getTooltipHtml((string)$warningText)}
</span>
HTML
            );

            $attrs = ['attribute_code' => $customAttribute];
            $attrs['selected'] = 'selected';
            $preparedAttributes[] = [
                'attrs' => $attrs,
                'value' => Settings::PACKAGE_MODE_CUSTOM_ATTRIBUTE,
                'label' => $this->magentoAttributeHelper
                    ->getAttributeLabel($customAttribute),
            ];
        }

        foreach ($textAttributes as $attribute) {
            $attrs = ['attribute_code' => $attribute['code']];

            if (
                $mode === Settings::PACKAGE_MODE_CUSTOM_ATTRIBUTE
                && $attribute['code'] == $customAttribute
            ) {
                $attrs['selected'] = 'selected';
            }
            $preparedAttributes[] = [
                'attrs' => $attrs,
                'value' => Settings::PACKAGE_MODE_CUSTOM_ATTRIBUTE,
                'label' => $attribute['label'],
            ];
        }

        return [$preparedAttributes, $warningToolTip];
    }

    private function getCustomValueInputHtml(string $type): string
    {
        $customValue = $this->settings->getPackageDimensionCustomValue($type);

        $classes = [
            'Temu-required-when-visible',
            'admin__control-text',
            'validator-greater-than-zero',
            'validator-temu-' . $type,
        ];

        $attributes = [
            'type' => 'text',
            'class' => implode(' ', $classes),
            'style' => 'max-width: 150px;',
            'id' => "package_{$type}_custom_value",
            'name' => "package_{$type}_custom_value",
            'value' => !empty($customValue) ? $customValue : null,
            'placeholder' => __('Enter value here'),
        ];

        if (
            $this->settings->getPackageDimensionMode($type)
            !== Settings::PACKAGE_MODE_CUSTOM_VALUE
        ) {
            $attributes['style'] .= 'display:none';
        }

        return '<input ' . $this->renderHtmlAttributes($attributes) . '>';
    }

    private function renderHtmlAttributes(array $attributes): string
    {
        $preparedAttributes = [];
        foreach ($attributes as $attributeName => $attributeValue) {
            $preparedAttributes[] = sprintf(
                '%s="%s"',
                $attributeName,
                $this->_escaper->escapeHtml($attributeValue)
            );
        }

        return implode(' ', $preparedAttributes);
    }

    private function getPackageDimensionLabel(string $type): string
    {
        $labels = [
            Settings::DIMENSION_TYPE_WEIGHT => __('Weight'),
            Settings::DIMENSION_TYPE_LENGTH => __('Length'),
            Settings::DIMENSION_TYPE_WIDTH => __('Width'),
            Settings::DIMENSION_TYPE_HEIGHT => __('Height'),
        ];

        return (string)($labels[$type] ?? 'N/A');
    }

    private function getPackageDimensionTooltipText(string $type): string
    {
        switch ($type) {
            case Settings::DIMENSION_TYPE_WEIGHT:
                return (string)__("The package weight must be a positive number. ( kg (EU), lb (US))");
            case Settings::DIMENSION_TYPE_LENGTH:
                return (string)__("The package length needs to be a whole number that's not negative. (cm (EU), in (US))");
            case Settings::DIMENSION_TYPE_WIDTH:
                return (string)__("The package width needs to be a whole number that's not negative. (cm (EU), in (US))");
            case Settings::DIMENSION_TYPE_HEIGHT:
                return (string)__("The package height needs to be a whole number that's not negative. (cm (EU), in (US))");
        }

        return 'N/A';
    }

    protected function _beforeToHtml()
    {
        $this->jsUrl->add(
            $this->getUrl('*/settings/save'),
            \M2E\Temu\Block\Adminhtml\Settings\Tabs::TAB_ID_MAIN
        );

        $this->jsPhp->addConstants(
            [
                '\M2E\Temu\Model\Settings::PACKAGE_MODE_CUSTOM_ATTRIBUTE' => \M2E\Temu\Model\Settings::PACKAGE_MODE_CUSTOM_ATTRIBUTE,
                '\M2E\Temu\Model\Settings::PACKAGE_MODE_CUSTOM_VALUE' => \M2E\Temu\Model\Settings::PACKAGE_MODE_CUSTOM_VALUE,
            ]
        );

        $jsSettings = json_encode(
            [
                'identifierSettings' => [
                    'valueModeAttribute' => \M2E\Temu\Model\Settings::VALUE_MODE_ATTRIBUTE,
                ],
            ]
        );
        $this->js->add(
            <<<JS
require([
    'Temu/Settings/Main'
], function(){
    window.TemuSettingsMainObj = new TemuSettingsMain($jsSettings);
});
JS
        );

        return parent::_beforeToHtml();
    }
}
