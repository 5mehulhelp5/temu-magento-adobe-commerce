<?php

namespace M2E\Temu\Block\Adminhtml\Listing\Create\Templates;

use M2E\Temu\Model\Listing;
use M2E\Temu\Model\Policy\Manager as TemplateManager;
use M2E\Temu\Model\ResourceModel\Policy\Shipping as ShippingResource;
use M2E\Temu\Model\ResourceModel\Policy\SellingFormat\CollectionFactory as SellingFormatCollectionFactory;
use M2E\Temu\Model\ResourceModel\Policy\Synchronization\CollectionFactory as SynchronizationCollectionFactory;
use M2E\Temu\Model\ResourceModel\Policy\Description\CollectionFactory as DescriptionCollectionFactory;
use M2E\Temu\Model\ResourceModel\Policy\Shipping\CollectionFactory as ShippingCollectionFactory;

class Form extends \M2E\Temu\Block\Adminhtml\Magento\Form\AbstractForm
{
    protected ?Listing $listing = null;
    private \M2E\Temu\Helper\Data\Session $sessionDataHelper;
    private Listing\Repository $listingRepository;
    private SellingFormatCollectionFactory $sellingFormatCollectionFactory;
    private SynchronizationCollectionFactory $synchronizationCollectionFactory;
    private DescriptionCollectionFactory $descriptionCollectionFactory;
    private ShippingCollectionFactory $shippingCollectionFactory;

    public function __construct(
        SellingFormatCollectionFactory $sellingFormatCollectionFactory,
        SynchronizationCollectionFactory $synchronizationCollectionFactory,
        DescriptionCollectionFactory $descriptionCollectionFactory,
        ShippingCollectionFactory $shippingCollectionFactory,
        \M2E\Temu\Model\Listing\Repository $listingRepository,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \M2E\Temu\Helper\Data\Session $sessionDataHelper,
        array $data = []
    ) {
        $this->sessionDataHelper = $sessionDataHelper;
        $this->listingRepository = $listingRepository;
        $this->sellingFormatCollectionFactory = $sellingFormatCollectionFactory;
        $this->synchronizationCollectionFactory = $synchronizationCollectionFactory;
        $this->descriptionCollectionFactory = $descriptionCollectionFactory;
        $this->shippingCollectionFactory = $shippingCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'method' => 'post',
                    'action' => $this->getUrl('*/listing/save'),
                ],
            ]
        );

        $formData = $this->getListingData();

        $form->addField(
            'account_id',
            'hidden',
            [
                'value' => $formData['account_id'],
            ]
        );

        $form->addField(
            'store_id',
            'hidden',
            [
                'value' => $formData['store_id'],
            ]
        );

        $fieldset = $form->addFieldset(
            'selling_settings',
            [
                'legend' => __('Selling'),
                'collapsable' => false,
            ]
        );

        $fieldset->addField(
            'template_selling_format_messages',
            self::CUSTOM_CONTAINER,
            [
                'style' => 'display: block;',
                'css_class' => 'Temu-fieldset-table no-margin-bottom',
            ]
        );

        $sellingFormatTemplates = $this->getSellingFormatTemplates();
        $style = count($sellingFormatTemplates) === 0 ? 'display: none' : '';

        $templateSellingFormatValue = $formData['template_selling_format_id'];
        if (empty($templateSellingFormatValue) && !empty($sellingFormatTemplates)) {
            $templateSellingFormatValue = reset($sellingFormatTemplates)['value'];
        }

        $templateSellingFormat = $this->elementFactory->create(
            'select',
            [
                'data' => [
                    'html_id' => 'template_selling_format_id',
                    'name' => 'template_selling_format_id',
                    'style' => 'width: 50%;' . $style,
                    'no_span' => true,
                    'values' => array_merge(['' => ''], $sellingFormatTemplates),
                    'value' => $templateSellingFormatValue,
                    'required' => true,
                ],
            ]
        );
        $templateSellingFormat->setForm($form);

        $style = count($sellingFormatTemplates) === 0 ? '' : 'display: none';
        $noPoliciesAvailableText = __('No Policies available.');
        $viewText = __('View');
        $editText = __('Edit');
        $orText = __('or');
        $addNewText = __('Add New');
        $fieldset->addField(
            'template_selling_format_container',
            self::CUSTOM_CONTAINER,
            [
                'label' => __('Selling Policy'),
                'style' => 'line-height: 34px;display: initial;',
                'field_extra_attributes' => 'style="margin-bottom: 5px"',
                'required' => true,
                'text' => <<<HTML
    <span id="template_selling_format_label" style="{$style}">
        $noPoliciesAvailableText
    </span>
    {$templateSellingFormat->toHtml()}
HTML
                ,
                'after_element_html' => <<<HTML
&nbsp;
<span style="line-height: 30px;">
    <span id="edit_selling_format_template_link" style="color:#41362f">
        <a href="javascript: void(0);" style="" onclick="TemuListingSettingsObj.editTemplate(
            '{$this->getEditUrl(TemplateManager::TEMPLATE_SELLING_FORMAT)}',
            $('template_selling_format_id').value,
            TemuListingSettingsObj.newSellingFormatTemplateCallback
        );">
            $viewText&nbsp;/&nbsp;$editText
        </a>
        <span>$orText</span>
    </span>
    <a id="add_selling_format_template_link" href="javascript: void(0);"
        onclick="TemuListingSettingsObj.addNewTemplate(
        '{$this->getAddNewUrl(TemplateManager::TEMPLATE_SELLING_FORMAT)}',
        TemuListingSettingsObj.newSellingFormatTemplateCallback
    );">$addNewText</a>
</span>
HTML
                ,
            ]
        );

        $descriptionTemplates = $this->getDescriptionTemplates();
        $style = count($descriptionTemplates) === 0 ? 'display: none' : '';

        $templateDescriptionValue = $formData['template_description_id'];
        if (empty($templateDescriptionValue) && !empty($descriptionTemplates)) {
            $templateDescriptionValue = reset($descriptionTemplates)['value'];
        }

        $templateDescription = $this->elementFactory->create(
            'select',
            [
                'data' => [
                    'html_id' => 'template_description_id',
                    'name' => 'template_description_id',
                    'style' => 'width: 50%;' . $style,
                    'no_span' => true,
                    'values' => array_merge(['' => ''], $descriptionTemplates),
                    'value' => $templateDescriptionValue,
                    'required' => true,
                ],
            ]
        );
        $templateDescription->setForm($form);

        $style = count($descriptionTemplates) === 0 ? '' : 'display: none';
        $fieldset->addField(
            'template_description_container',
            self::CUSTOM_CONTAINER,
            [
                'label' => __('Description Policy'),
                'style' => 'line-height: 34px;display: initial;',
                'field_extra_attributes' => 'style="margin-bottom: 5px"',
                'required' => true,
                'text' => <<<HTML
    <span id="template_description_label" style="{$style}">
        $noPoliciesAvailableText
    </span>
    {$templateDescription->toHtml()}
HTML
                ,
                'after_element_html' => <<<HTML
&nbsp;
<span style="line-height: 30px;">
    <span id="edit_description_template_link" style="color:#41362f">
        <a href="javascript: void(0);" onclick="TemuListingSettingsObj.editTemplate(
            '{$this->getEditUrl(TemplateManager::TEMPLATE_DESCRIPTION)}',
            $('template_description_id').value,
            TemuListingSettingsObj.newDescriptionTemplateCallback
        );">
            $viewText&nbsp;/&nbsp;$editText
        </a>
        <span>$orText</span>
    </span>
    <a id="add_description_template_link" href="javascript: void(0);"
        onclick="TemuListingSettingsObj.addNewTemplate(
        '{$this->getAddNewUrl(TemplateManager::TEMPLATE_DESCRIPTION)}',
        TemuListingSettingsObj.newDescriptionTemplateCallback
    );">$addNewText</a>
</span>
HTML
                ,
            ]
        );

        $fieldset = $form->addFieldset(
            'shipping_settings',
            [
                'legend' => __('Shipping'),
                'collapsable' => false,
            ]
        );

        $accountId = (int)$formData['account_id'];
        $shippingTemplates = $this->getShippingTemplates($accountId);
        $style = count($shippingTemplates) === 0 ? 'display: none' : '';

        $shippingTemplatesValue = $formData['template_shipping_id'];
        if (empty($shippingTemplatesValue) && !empty($shippingTemplates)) {
            $shippingTemplatesValue = reset($shippingTemplates)['value'];
        }

        $templateShipping = $this->elementFactory->create(
            'select',
            [
                'data' => [
                    'html_id' => 'template_shipping_id',
                    'name' => 'template_shipping_id',
                    'style' => 'width: 50%;' . $style,
                    'no_span' => true,
                    'values' => array_merge(['' => ''], $shippingTemplates),
                    'value' => $shippingTemplatesValue,
                    'required' => true,
                ],
            ]
        );
        $templateShipping->setForm($form);

        $style = count($shippingTemplates) === 0 ? '' : 'display: none';
        $fieldset->addField(
            'template_shipping_container',
            self::CUSTOM_CONTAINER,
            [
                'label' => __('Shipping Policy'),
                'style' => 'line-height: 34px;display: initial;',
                'field_extra_attributes' => 'style="margin-bottom: 5px"',
                'required' => true,
                'text' => <<<HTML
    <span id="template_shipping_label" style="{$style}">
        $noPoliciesAvailableText
    </span>
    {$templateShipping->toHtml()}
HTML
                ,
                'after_element_html' => <<<HTML
&nbsp;
<span style="line-height: 30px;">
    <span id="edit_shipping_template_link" style="color:#41362f">
        <a href="javascript: void(0);" onclick="TemuListingSettingsObj.editTemplate(
            '{$this->getEditUrl(TemplateManager::TEMPLATE_SHIPPING)}',
            $('template_shipping_id').value,
            TemuListingSettingsObj.newShippingTemplateCallback
        );">
            $viewText&nbsp;/&nbsp;$editText
        </a>
        <span>$orText</span>
    </span>
    <a id="add_shipping_template_link" href="javascript: void(0);"
        onclick="TemuListingSettingsObj.addNewTemplate(
        '{$this->getAddNewUrl( TemplateManager::TEMPLATE_SHIPPING, $accountId)}',
        TemuListingSettingsObj.newShippingTemplateCallback
    );">$addNewText</a>
</span>
HTML
                ,
            ]
        );

        $fieldset = $form->addFieldset(
            'synchronization_settings',
            [
                'legend' => __('Synchronization'),
                'collapsable' => false,
            ]
        );

        $synchronizationTemplates = $this->getSynchronizationTemplates();
        $style = count($synchronizationTemplates) === 0 ? 'display: none' : '';

        $templateSynchronizationValue = $formData['template_synchronization_id'];
        if (empty($templateSynchronizationValue) && !empty($synchronizationTemplates)) {
            $templateSynchronizationValue = reset($synchronizationTemplates)['value'];
        }

        $templateSynchronization = $this->elementFactory->create(
            'select',
            [
                'data' => [
                    'html_id' => 'template_synchronization_id',
                    'name' => 'template_synchronization_id',
                    'style' => 'width: 50%;' . $style,
                    'no_span' => true,
                    'values' => array_merge(['' => ''], $synchronizationTemplates),
                    'value' => $templateSynchronizationValue,
                    'required' => true,
                ],
            ]
        );
        $templateSynchronization->setForm($form);

        $style = count($synchronizationTemplates) === 0 ? '' : 'display: none';
        $fieldset->addField(
            'template_synchronization_container',
            self::CUSTOM_CONTAINER,
            [
                'label' => __('Synchronization Policy'),
                'style' => 'line-height: 34px;display: initial;',
                'field_extra_attributes' => 'style="margin-bottom: 5px"',
                'required' => true,
                'text' => <<<HTML
    <span id="template_synchronization_label" style="{$style}">
        $noPoliciesAvailableText
    </span>
    {$templateSynchronization->toHtml()}
HTML
                ,
                'after_element_html' => <<<HTML
&nbsp;
<span style="line-height: 30px;">
    <span id="edit_synchronization_template_link" style="color:#41362f">
        <a href="javascript: void(0);" onclick="TemuListingSettingsObj.editTemplate(
            '{$this->getEditUrl(TemplateManager::TEMPLATE_SYNCHRONIZATION)}',
            $('template_synchronization_id').value,
            TemuListingSettingsObj.newSynchronizationTemplateCallback
        );">
            $viewText&nbsp;/&nbsp;$editText
        </a>
        <span>$orText</span>
    </span>
    <a id="add_synchronization_template_link" href="javascript: void(0);"
        onclick="TemuListingSettingsObj.addNewTemplate(
        '{$this->getAddNewUrl(TemplateManager::TEMPLATE_SYNCHRONIZATION)}',
        TemuListingSettingsObj.newSynchronizationTemplateCallback
    );">$addNewText</a>
</span>
HTML
                ,
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _prepareLayout()
    {
        $formData = $this->getListingData();

        $this->jsUrl->addUrls(
            [
                'templateCheckMessages' => $this->getUrl('*/policy/checkMessages'),
                'getShippingTemplates' => $this->getUrl(
                    '*/general/modelGetAll',
                    [
                        'model' => 'Policy_Shipping',
                        'id_field' => 'id',
                        'data_field' => 'title',
                        'sort_field' => 'title',
                        'sort_dir' => 'ASC',
                        'account_id' => $formData['account_id'],
                    ]
                ),
                'getReturnPolicyTemplates' => $this->getUrl(
                    '*/general/modelGetAll',
                    [
                        'model' => 'Temu_Template_ReturnPolicy',
                        'id_field' => 'id',
                        'data_field' => 'title',
                        'sort_field' => 'title',
                        'is_custom_template' => 0,
                    ]
                ),
                'getSellingFormatTemplates' => $this->getUrl(
                    '*/general/modelGetAll',
                    [
                        'model' => 'Policy_SellingFormat',
                        'id_field' => 'id',
                        'data_field' => 'title',
                        'sort_field' => 'title',
                        'sort_dir' => 'ASC',
                        'is_custom_template' => 0,
                    ]
                ),
                'getSynchronizationTemplates' => $this->getUrl(
                    '*/general/modelGetAll',
                    [
                        'model' => 'Policy_Synchronization',
                        'id_field' => 'id',
                        'data_field' => 'title',
                        'sort_field' => 'title',
                        'sort_dir' => 'ASC',
                        'is_custom_template' => 0,
                    ]
                ),
                'getDescriptionTemplates' => $this->getUrl(
                    '*/general/modelGetAll',
                    [
                        'model' => 'Policy_Description',
                        'id_field' => 'id',
                        'data_field' => 'title',
                        'sort_field' => 'title',
                        'sort_dir' => 'ASC',
                        'is_custom_template' => 0,
                    ]
                ),
            ]
        );

        $this->js->addOnReadyJs(
            <<<JS
    require([
        'Temu/TemplateManager',
        'Temu/Listing/Settings'
    ], function(){
        TemplateManagerObj = new TemplateManager();
        TemuListingSettingsObj = new TemuListingSettings();
        TemuListingSettingsObj.initObservers();
    });
JS
        );

        return parent::_prepareLayout();
    }

    public function getDefaultFieldsValues()
    {
        return [
            'template_selling_format_id' => '',
            'template_description_id' => '',
            'template_synchronization_id' => '',
            'template_shipping_id' => '',
        ];
    }

    protected function getListingData(): ?array
    {
        if ($this->getRequest()->getParam('id') !== null) {
            $data = array_merge($this->getListing()->getData(), $this->getListing()->getData());
        } else {
            $data = $this->sessionDataHelper->getValue(Listing::CREATE_LISTING_SESSION_DATA);
            $data = array_merge($this->getDefaultFieldsValues(), $data);
        }

        return $data;
    }

    protected function getListing(): ?Listing
    {
        $listingId = $this->getRequest()->getParam('id');
        if ($this->listing === null && $listingId) {
            $this->listing = $this->listingRepository->get((int)$listingId);
        }

        return $this->listing;
    }

    protected function getSellingFormatTemplates()
    {
        $collection = $this->sellingFormatCollectionFactory->create();
        $collection->addFieldToFilter('is_custom_template', 0);
        $collection->setOrder('title', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

        $collection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS)->columns(
            [
                'value' => \M2E\Temu\Model\ResourceModel\Policy\SellingFormat::COLUMN_ID,
                'label' => \M2E\Temu\Model\ResourceModel\Policy\SellingFormat::COLUMN_TITLE,
            ]
        );

        $result = $collection->toArray();

        return $result['items'];
    }

    protected function getSynchronizationTemplates(): array
    {
        $collection = $this->synchronizationCollectionFactory->create();
        $collection->addFieldToFilter('is_custom_template', 0);
        $collection->setOrder('title', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

        $collection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS)->columns(
            [
                'value' => \M2E\Temu\Model\ResourceModel\Policy\Synchronization::COLUMN_ID,
                'label' => \M2E\Temu\Model\ResourceModel\Policy\Synchronization::COLUMN_TITLE,
            ]
        );

        return $collection->getConnection()->fetchAssoc($collection->getSelect());
    }

    protected function getDescriptionTemplates(): array
    {
        $collection = $this->descriptionCollectionFactory->create();
        $collection->addFieldToFilter('is_custom_template', 0);
        $collection->setOrder('title', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

        $collection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS)->columns(
            [
                'value' => \M2E\Temu\Model\ResourceModel\Policy\Description::COLUMN_ID,
                'label' => \M2E\Temu\Model\ResourceModel\Policy\Description::COLUMN_TITLE,
            ]
        );

        return $collection->getConnection()->fetchAssoc($collection->getSelect());
    }

    protected function getShippingTemplates(int $accountId): array
    {
        $collection = $this->shippingCollectionFactory->create();
        $collection->addFieldToFilter(ShippingResource::COLUMN_ACCOUNT_ID, ['eq' => $accountId]);
        $collection->setOrder(ShippingResource::COLUMN_TITLE, \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

        $collection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS)
                   ->columns(
                       [
                           'value' => ShippingResource::COLUMN_ID,
                           'label' => ShippingResource::COLUMN_TITLE,
                       ]
                   );

        $result = $collection->toArray();

        return $result['items'];
    }

    protected function getAddNewUrl($nick, ?int $accountId = null)
    {
        $params = [
            'wizard' => $this->getRequest()->getParam('wizard'),
            'nick' => $nick,
            'close_on_save' => 1,
        ];

        if ($accountId !== null) {
            $params['account_id'] = $accountId;
        }

        return $this->getUrl('*/policy/newAction', $params);
    }

    protected function getEditUrl($nick)
    {
        return $this->getUrl(
            '*/policy/edit',
            [
                'wizard' => $this->getRequest()->getParam('wizard'),
                'nick' => $nick,
                'close_on_save' => 1,
            ]
        );
    }
}
