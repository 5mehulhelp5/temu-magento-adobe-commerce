<?php

namespace M2E\Temu\Block\Adminhtml\Template\Synchronization\Edit\Form\Tabs;

class ReviseRules extends AbstractTab
{
    private \M2E\Temu\Model\Policy\Synchronization\Builder $synchronizationBuilder;

    public function __construct(
        \M2E\Temu\Model\Policy\Synchronization\Builder $synchronizationBuilder,
        \M2E\Temu\Helper\Data\GlobalData $globalDataHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct(
            $globalDataHelper,
            $context,
            $registry,
            $formFactory,
            $data
        );
        $this->synchronizationBuilder = $synchronizationBuilder;
    }

    protected function _prepareForm()
    {
        $default = $this->synchronizationBuilder->getDefaultData();
        $formData = $this->getFormData();

        $formData = array_merge($default, $formData);

        $form = $this->_formFactory->create();

        $form->addField(
            'template_synchronization_form_data_revise',
            self::HELP_BLOCK,
            [
                'content' => __(
                    '<p>Specify which Channel data should be automatically revised ' .
                    'by %extension_title.</p><br> <p>Selected Item Properties will be automatically updated based ' .
                    'on the changes in related Magento Attributes or Policy Templates.</p><br>' .
                    '<p>More detailed information on how to work with this Page can be found ' .
                    '<a href="%url" target="_blank" class="external-link">here</a>.</p>',
                    [
                        'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                        'url' => 'https://docs-m2.m2epro.com/docs/revise-rules-for-temu-listings/'
                    ],
                ),
            ]
        );

        $fieldset = $form->addFieldset(
            'magento_block_template_synchronization_form_data_revise_products',
            [
                'legend' => __('Revise Conditions'),
                'collapsable' => true,
            ]
        );

        $fieldset->addField(
            'revise_update_qty',
            self::SELECT,
            [
                'name' => 'synchronization[revise_update_qty]',
                'label' => __('Quantity'),
                'value' => $formData['revise_update_qty'],
                'values' => [
                    1 => __('Yes'),
                ],
                'disabled' => true,
            ]
        );

        $fieldset->addField(
            'revise_update_qty_max_applied_value_mode',
            self::SELECT,
            [
                'container_id' => 'revise_update_qty_max_applied_value_mode_tr',
                'name' => 'synchronization[revise_update_qty_max_applied_value_mode]',
                'label' => __('Conditional Revise'),
                'value' => $formData['revise_update_qty_max_applied_value_mode'],
                'values' => [
                    0 => __('Disabled'),
                    1 => __('Revise When Less or Equal to'),
                ],
            ]
        )->setAfterElementHtml(
            <<<HTML
<input name="synchronization[revise_update_qty_max_applied_value]" id="revise_update_qty_max_applied_value"
       value="{$formData['revise_update_qty_max_applied_value']}" type="text"
       style="width: 72px; margin-left: 10px;"
       class="input-text admin__control-text required-entry Temu-validate-qty _required" />
HTML
        );

        $fieldset->addField(
            'revise_update_qty_max_applied_value_line_tr',
            self::SEPARATOR,
            []
        );

        $fieldset->addField(
            'revise_update_price',
            self::SELECT,
            [
                'name' => 'synchronization[revise_update_price]',
                'label' => __('Price'),
                'value' => $formData['revise_update_price'],
                'values' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );

        $form->addField(
            'revise_qty_max_applied_value_confirmation_popup_template',
            self::CUSTOM_CONTAINER,
            [
                'text' => __(
                    '<br/>Disabling this option might affect synchronization performance. ' .
                    'Please read <a href="%url" target="_blank">this article</a> before using the option.',
                    ['url' => 'https://help.m2epro.com/support/solutions/articles/9000200401']
                ),
                'style' => 'display: none;',
            ]
        );

        $fieldset->addField(
            'revise_update_title',
            self::SELECT,
            [
                'name' => 'synchronization[revise_update_title]',
                'label' => __('Title'),
                'value' => $formData['revise_update_title'],
                'values' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'tooltip' => __(
                    'Automatically revises Item Title on %channel_title when Product Name, Magento Attribute
                    used for Item Title or Custom Title value are modified in Magento or Policy Template.',
                    [
                        'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                    ]
                ),
            ]
        );

        $fieldset->addField(
            'revise_update_description',
            self::SELECT,
            [
                'name' => 'synchronization[revise_update_description]',
                'label' => __('Description'),
                'value' => $formData['revise_update_description'],
                'values' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'tooltip' => __(
                    'Automatically revises Item Description on %channel_title when Product Description, Product Short
                    Description or Custom Description value are modified in Magento or Policy Template.',
                    [
                        'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                    ]
                ),
            ]
        );

        $fieldset->addField(
            'revise_update_images',
            self::SELECT,
            [
                'name' => 'synchronization[revise_update_images]',
                'label' => __('Images'),
                'value' => $formData['revise_update_images'],
                'values' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'tooltip' => __(
                    'Automatically revises Item Image(s) on %channel_title when Product Image(s)
                    or Magento Attribute used for Product Image(s) are modified in Magento or Policy Template.',
                    [
                        'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                    ]
                ),
            ]
        );

        $fieldset->addField(
            'revise_update_categories',
            self::SELECT,
            [
                'name' => 'synchronization[revise_update_categories]',
                'label' => __('Attributes'),
                'value' => $formData['revise_update_categories'],
                'values' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'tooltip' => __(
                    'Automatically revises Item Attributes on %channel_title when Attributes
                    data or Magento Attributes used for Attributes are modified.',
                    [
                        'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                    ]
                ),
            ]
        );

        $fieldset->addField(
            'revise_update_shipping',
            self::SELECT,
            [
                'name' => 'synchronization[revise_update_shipping]',
                'label' => __('Shipping'),
                'value' => $formData['revise_update_shipping'],
                'values' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'tooltip' => __(
                    'Automatically revises Shipping on %channel_title when Shipping modified in Policy Template.',
                    [
                        'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                    ]
                ),
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
