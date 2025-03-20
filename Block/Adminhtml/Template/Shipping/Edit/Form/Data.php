<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Template\Shipping\Edit\Form;

class Data extends \M2E\Temu\Block\Adminhtml\Magento\Form\AbstractForm
{
    private \M2E\Temu\Helper\Data\GlobalData $globalDataHelper;

    public function __construct(
        \M2E\Temu\Helper\Data\GlobalData $globalDataHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->globalDataHelper = $globalDataHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm(): Data
    {
        $formData = $this->getFormData();
        $default = $this->getDefault();
        $formData = array_merge($default, $formData);

        $form = $this->_formFactory->create();

        $form->addField(
            'shipping_id',
            'hidden',
            [
                'name' => 'shipping[id]',
                'value' => $formData['id'] ?? '',
            ]
        );

        $form->addField(
            'shipping_title',
            'hidden',
            [
                'name' => 'shipping[title]',
                'value' => $this->getTitle(),
            ]
        );

        $fieldset = $form->addFieldset(
            'magento_block_template_shipping_edit_form',
            [
                'legend' => __('Channel'),
                'collapsable' => false,
            ]
        );

        $style = empty($formData['account_id']) ? 'display: none;' : '';

        $fieldset->addField(
            'shipping_template_id',
            self::SELECT,
            [
                'name' => 'shipping[shipping_template_id]',
                'label' => __('Template'),
                'title' => __('Template'),
                'required' => true,
                'style' => 'max-width: 30%;',
                'after_element_html' => $this->createButtonsBlock(
                    [
                        $this->getRefreshButtonHtml(
                            'refresh_templates',
                            'TemuTemplateShippingObj.updateTemplates(true);',
                            $style
                        ),
                    ],
                    $style
                ),
            ]
        );

        $handlingModeOptions = $this->getPreparationTimeOptions();

        $fieldset->addField(
            'preparation_time',
            self::SELECT,
            [
                'name' => 'shipping[preparation_time]',
                'label' => __('Preparation Time'),
                'title' => __('Preparation Time'),
                'values' => $handlingModeOptions,
                'value' => $formData['preparation_time'],
                'class' => 'admin__control-select Temu-validate-handling-time',
                'required' => true,
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    private function getTitle()
    {
        $template = $this->globalDataHelper->getValue('temu_template_shipping');

        if ($template === null) {
            return '';
        }

        return $template->getTitle();
    }

    private function getFormData()
    {
        $template = $this->globalDataHelper->getValue('temu_template_shipping');

        if ($template === null || $template->getId() === null) {
            return [];
        }

        return $template->getData();
    }

    private function getDefault(): array
    {
        return [
            'shipping_template_id' => '',
            'preparation_time' => '',
        ];
    }

    protected function _toHtml()
    {
        $formData = $this->getFormData();
        $currentAccountId = $formData['account_id'] ?? null;
        $currentShippingTemplateId = $formData['shipping_template_id'] ?? null;

        $urlGetTemplates = $this->getUrl('*/policy_shipping/templateList');

        $this->js->add(
            <<<JS
    require([
        'Temu/Template/Shipping'
        ], function() {
    window.TemuTemplateShippingObj = new TemuTemplateShipping({
            accountId: '$currentAccountId',
            shippingTemplateId: '$currentShippingTemplateId',
            urlGetTemplates: '$urlGetTemplates',
        });
    });
JS
        );

        return parent::_toHtml();
    }

    /**
     * @param string[] $actions
     *
     * @return string
     */
    private function createButtonsBlock(array $actions, string $style): string
    {
        $formattedActions = [];
        /** @var string $action */
        foreach ($actions as $action) {
            $formattedActions[] = sprintf('<span class="action">%s</span>', $action);
        }

        return sprintf(
            '<span class="actions" style="%s">%s</span>',
            $style,
            implode(' ', $formattedActions)
        );
    }

    private function getRefreshButtonHtml(string $id, string $onClick, string $style): string
    {
        $data = [
            'id' => $id,
            'label' => __('Refresh Templates'),
            'onclick' => $onClick,
            'class' => 'refresh_status primary',
            'style' => $style,
        ];

        return $this->getLayout()
                    ->createBlock(\M2E\Temu\Block\Adminhtml\Magento\Button::class)
                    ->setData($data)
                    ->toHtml();
    }

    public function getPreparationTimeOptions(): array
    {
        $formData = $this->getFormData();
        $default = $this->getDefault();
        $formData = array_merge($default, $formData);

        $handlingOptions = [
            [
                "value" => "1",
                "label" => "1 Business Day"
            ],
            [
                "value" => "2",
                "label" => "2 Business Day"
            ],
        ];

        if (!isset($formData['preparation_time']) || $formData['preparation_time'] === '') {
            array_unshift($handlingOptions, [
                "value" => "",
                "label" => "Not Set"
            ]);
        }

        return $handlingOptions;
    }
}
