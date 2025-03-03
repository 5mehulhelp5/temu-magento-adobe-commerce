<?php

namespace M2E\Temu\Block\Adminhtml\Listing\Template\Switcher;

class Initialization extends \M2E\Temu\Block\Adminhtml\Magento\AbstractBlock
{
    /** @var \M2E\Temu\Helper\Data */
    private $dataHelper;
    /** @var \M2E\Temu\Helper\Data\GlobalData */
    private $globalDataHelper;

    public function __construct(
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \M2E\Temu\Helper\Data $dataHelper,
        \M2E\Temu\Helper\Data\GlobalData $globalDataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->globalDataHelper = $globalDataHelper;
    }

    public function _construct()
    {
        parent::_construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('TemuListingTemplateSwitcherInitialization');
        // ---------------------------------------
    }

    protected function _toHtml()
    {
        // ---------------------------------------
        $urls = [];

        // initiate account param
        // ---------------------------------------
        $account = $this->globalDataHelper->getValue('temu_account');
        $params['account_id'] = $account->getId();
        // ---------------------------------------

        // initiate attribute sets param
        // ---------------------------------------
        if (
            $this->getMode(
            ) == \M2E\Temu\Block\Adminhtml\Listing\Template\Switcher::MODE_LISTING_PRODUCT
        ) {
            $attributeSets = $this->globalDataHelper->getValue('temu_attribute_sets');
            $params['attribute_sets'] = implode(',', $attributeSets);
        }
        // ---------------------------------------

        // initiate display use default option param
        // ---------------------------------------
        $displayUseDefaultOption = $this->globalDataHelper->getValue('temu_display_use_default_option');
        $params['display_use_default_option'] = (int)(bool)$displayUseDefaultOption;
        // ---------------------------------------

        $path = 'policy/getTemplateHtml';
        $urls[$path] = $this->getUrl('*/' . $path, $params);
        //------------------------------

        //------------------------------
        $path = 'policy/isTitleUnique';
        $urls[$path] = $this->getUrl('*/' . $path);

        $path = 'policy/newTemplateHtml';
        $urls[$path] = $this->getUrl('*/' . $path);

        $path = 'policy/edit';
        $urls[$path] = $this->getUrl(
            '*/policy/edit',
            ['wizard' => (bool)$this->getRequest()->getParam('wizard', false)]
        );
        //------------------------------

        $this->jsUrl->addUrls($urls);
        $this->jsUrl->add(
            $this->getUrl('*/policy/checkMessages'),
            'templateCheckMessages'
        );

        $this->jsPhp->addConstants(
            [
                '\M2E\Temu\Model\Policy\Manager::TEMPLATE_DESCRIPTION' => \M2E\Temu\Model\Policy\Manager::TEMPLATE_DESCRIPTION,
                '\M2E\Temu\Model\Policy\Manager::MODE_PARENT' => \M2E\Temu\Model\Policy\Manager::MODE_PARENT,
                '\M2E\Temu\Model\Policy\Manager::MODE_CUSTOM' => \M2E\Temu\Model\Policy\Manager::MODE_CUSTOM,
                '\M2E\Temu\Model\Policy\Manager::MODE_TEMPLATE' => \M2E\Temu\Model\Policy\Manager::MODE_TEMPLATE,
                '\M2E\Temu\Model\Policy\Manager::TEMPLATE_SELLING_FORMAT' => \M2E\Temu\Model\Policy\Manager::TEMPLATE_SELLING_FORMAT,
                '\M2E\Temu\Model\Policy\Manager::TEMPLATE_SYNCHRONIZATION' => \M2E\Temu\Model\Policy\Manager::TEMPLATE_SYNCHRONIZATION,
            ]
        );

        $this->jsTranslator->addTranslations([
            'Customized' => __('Customized'),
            'Policies' => __('Policies'),
            'Policy with the same Title already exists.' => __('Policy with the same Title already exists.'),
            'Please specify Policy Title' => __('Please specify Policy Title'),
            'Save New Policy' => __('Save New Policy'),
            'Save as New Policy' => __('Save as New Policy'),
        ]);

        $store = $this->globalDataHelper->getValue('temu_store');

        $this->js->add(
            <<<JS
    define('Switcher/Initialization',[
        'Temu/Listing/Template/Switcher',
        'Temu/TemplateManager'
    ], function(){
        window.TemplateManagerObj = new TemplateManager();

        window.TemuListingTemplateSwitcherObj = new TemuListingTemplateSwitcher();
        TemuListingTemplateSwitcherObj.storeId = {$store->getId()};
        TemuListingTemplateSwitcherObj.listingProductIds = '{$this->getRequest()->getParam('ids')}';

    });
JS
        );

        return parent::_toHtml();
    }
}
