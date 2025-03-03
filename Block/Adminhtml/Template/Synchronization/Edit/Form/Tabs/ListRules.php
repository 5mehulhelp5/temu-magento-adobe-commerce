<?php

namespace M2E\Temu\Block\Adminhtml\Template\Synchronization\Edit\Form\Tabs;

use M2E\Temu\Model\Policy\Synchronization as TemplateSynchronization;

class ListRules extends AbstractTab
{
    /** @var \M2E\Temu\Model\Policy\Synchronization\Builder */
    private TemplateSynchronization\Builder $synchronizationBuilder;
    private \M2E\Temu\Model\Magento\Product\RuleFactory $ruleFactory;

    public function __construct(
        \M2E\Temu\Model\Policy\Synchronization\Builder $synchronizationBuilder,
        \M2E\Temu\Model\Magento\Product\RuleFactory $ruleFactory,
        \M2E\Temu\Helper\Data\GlobalData $globalDataHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->synchronizationBuilder = $synchronizationBuilder;
        $this->ruleFactory = $ruleFactory;
        parent::__construct(
            $globalDataHelper,
            $context,
            $registry,
            $formFactory,
            $data
        );
    }

    protected function _prepareForm()
    {
        $default = $this->synchronizationBuilder->getDefaultData();
        $formData = $this->getFormData();

        $formData = array_merge($default, $formData);

        $form = $this->_formFactory->create();

        $form->addField(
            'synchronization_id',
            'hidden',
            [
                'name' => 'synchronization[id]',
                'value' => (!$this->isCustom() && isset($formData['id'])) ? (int)$formData['id'] : '',
            ]
        );

        $form->addField(
            'synchronization_title',
            'hidden',
            [
                'name' => 'synchronization[title]',
                'value' => $this->getTitle(),
            ]
        );

        $form->addField(
            'synchronization_is_custom_template',
            'hidden',
            [
                'name' => 'synchronization[is_custom_template]',
                'value' => $this->isCustom() ? 1 : 0,
            ]
        );

        $form->addField(
            'template_synchronization_form_data_list',
            self::HELP_BLOCK,
            [
                'content' => __(
                    '<p><strong>List Action</strong> - this Action can be executed for each ' .
                    'Item in %extension_title Listings which has <strong>Not Listed</strong> ' .
                    'Status and which Settings meet the List Condition. If an Item was not initially Listed for ' .
                    'some reason, automatic synchronization will attempt to list it again only if there is a ' .
                    'change of Product Status, Stock Availability or Quantity in Magento.</p><br>' .
                    '<p><strong>Note:</strong> %extension_title Listings Synchronization must be enabled in ' .
                    'Synchronization <strong>(%channel_title > Configuration > Settings > Synchronization)' .
                    '</strong>. Otherwise, Synchronization Policy Rules will not take effect.</p><br>' .
                    '<p>More detailed information about how to work with this Page you can find ' .
                    '<a href="%url" target="_blank" class="external-link">here</a>.</p>',
                    [
                        'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                        'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                        'url' => 'https://docs-m2.m2epro.com/docs/list-rules-for-temu-listings/'
                    ],
                ),
            ]
        );

        $fieldset = $form->addFieldset(
            'magento_block_template_synchronization_form_data_list',
            [
                'legend' => __('General'),
                'collapsable' => false,
            ]
        );

        $fieldset->addField(
            'list_mode',
            self::SELECT,
            [
                'name' => 'synchronization[list_mode]',
                'label' => __('List Action'),
                'value' => $formData['list_mode'],
                'values' => [
                    0 => __('Disabled'),
                    1 => __('Enabled'),
                ],
            ]
        );

        $fieldset = $form->addFieldset(
            'magento_block_template_synchronization_form_data_list_rules',
            [
                'legend' => __('List Conditions'),
                'collapsable' => false,
            ]
        );

        $fieldset->addField(
            'list_status_enabled',
            self::SELECT,
            [
                'name' => 'synchronization[list_status_enabled]',
                'label' => __('Product Status'),
                'value' => $formData['list_status_enabled'],
                'values' => [
                    0 => __('Any'),
                    1 => __('Enabled'),
                ],
            ]
        );

        $fieldset->addField(
            'list_is_in_stock',
            self::SELECT,
            [
                'name' => 'synchronization[list_is_in_stock]',
                'label' => __('Stock Availability'),
                'value' => $formData['list_is_in_stock'],
                'values' => [
                    0 => __('Any'),
                    1 => __('In Stock'),
                ],
            ]
        );

        $fieldset->addField(
            'list_qty_calculated',
            self::SELECT,
            [
                'name' => 'synchronization[list_qty_calculated]',
                'label' => __('Quantity'),
                'value' => $formData['list_qty_calculated'],
                'values' => [
                    TemplateSynchronization::QTY_MODE_NONE => __('Any'),
                    TemplateSynchronization::QTY_MODE_YES => __('More or Equal'),
                ],
            ]
        )->setAfterElementHtml(
            <<<HTML
<input name="synchronization[list_qty_calculated_value]" id="list_qty_calculated_value"
       value="{$formData['list_qty_calculated_value']}" type="text"
       style="width: 72px; margin-left: 10px;"
       class="input-text admin__control-text required-entry validate-digits _required" />
HTML
        );

        $fieldset = $form->addFieldset(
            'magento_block_template_synchronization_list_advanced_filters',
            [
                'legend' => __('Advanced Conditions'),
                'collapsable' => false,
            ]
        );

        $fieldset->addField(
            'list_advanced_rules_filters_warning',
            self::MESSAGES,
            [
                'messages' => [
                    [
                        'type' => \Magento\Framework\Message\MessageInterface::TYPE_WARNING,
                        'content' => __('Please be very thoughtful before enabling this option as ' .
                            'this functionality can have a negative impact on the Performance of ' .
                            'your system.<br> It can decrease the speed of running in case you have ' .
                            'a lot of Products with the high number of changes made to them.'),
                    ],
                ],
            ]
        );

        $fieldset->addField(
            'list_advanced_rules_mode',
            self::SELECT,
            [
                'name' => 'synchronization[list_advanced_rules_mode]',
                'label' => __('Mode'),
                'value' => $formData['list_advanced_rules_mode'],
                'values' => [
                    0 => __('Disabled'),
                    1 => __('Enabled'),
                ],
            ]
        );

        $ruleModel = $this->ruleFactory->create()
                                       ->setData(
                                           ['prefix' => TemplateSynchronization::LIST_ADVANCED_RULES_PREFIX],
                                       );

        if (!empty($formData['list_advanced_rules_filters'])) {
            $ruleModel->loadFromSerialized($formData['list_advanced_rules_filters']);
        }

        $ruleBlock = $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Magento\Product\Rule::class)
                          ->setData(['rule_model' => $ruleModel]);

        $fieldset->addField(
            'advanced_filter',
            self::CUSTOM_CONTAINER,
            [
                'container_id' => 'list_advanced_rules_filters_container',
                'label' => __('Conditions'),
                'text' => $ruleBlock->toHtml(),
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
