<?php

namespace M2E\Temu\Block\Adminhtml\Listing;

use M2E\Temu\Block\Adminhtml\Log\AbstractGrid;

class View extends \M2E\Temu\Block\Adminhtml\Magento\Grid\AbstractContainer
{
    private \M2E\Core\Helper\Url $urlHelper;
    private string $viewMode;
    private \M2E\Temu\Model\Listing\Ui\RuntimeStorage $uiListingRuntimeStorage;

    public function __construct(
        \M2E\Temu\Model\Listing\Ui\RuntimeStorage $uiListingRuntimeStorage,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Widget $context,
        \M2E\Core\Helper\Url $urlHelper,
        array $data = []
    ) {
        $this->uiListingRuntimeStorage = $uiListingRuntimeStorage;
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();

        /** @var \M2E\Temu\Block\Adminhtml\Listing\View\Switcher $viewModeSwitcher */
        $viewModeSwitcher = $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Listing\View\Switcher::class);

        // Initialization block
        // ---------------------------------------
        $this->setId('temuListingView');
        $this->_controller = 'adminhtml_listing_view_' . $viewModeSwitcher->getSelectedParam();
        $this->viewMode = $viewModeSwitcher->getSelectedParam();
        // ---------------------------------------

        // Set buttons actions
        // ---------------------------------------
        $this->removeButton('add');
        // ---------------------------------------
    }

    protected function _prepareLayout()
    {
        $this->jsPhp->addConstants(
            [
                '\M2E\Temu\Block\Adminhtml\Log\Listing\Product\AbstractGrid::LISTING_PRODUCT_ID_FIELD' => AbstractGrid::LISTING_PRODUCT_ID_FIELD,
            ]
        );

        // ---------------------------------------
        $backUrl = $this->urlHelper->getBackUrl('*/listing/index');

        $this->addButton(
            'back',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $backUrl . '\');',
                'class' => 'back',
            ]
        );
        // ---------------------------------------

        // ---------------------------------------
        $url = $this->getUrl(
            '*/log_listing_product',
            [
                \M2E\Temu\Block\Adminhtml\Log\AbstractGrid::LISTING_ID_FIELD =>
                    $this->uiListingRuntimeStorage->getListing()->getId(),
            ]
        );
        $this->addButton(
            'view_log',
            [
                'label' => __('Logs & Events'),
                'onclick' => 'window.open(\'' . $url . '\',\'_blank\')',
            ]
        );
        // ---------------------------------------

        // ---------------------------------------
        $this->addButton(
            'edit_templates',
            [
                'label' => __('Edit Settings'),
                'onclick' => '',
                'class' => 'drop_down edit_default_settings_drop_down primary',
                'class_name' => \M2E\Temu\Block\Adminhtml\Magento\Button\DropDown::class,
                'options' => $this->getSettingsButtonDropDownItems(),
            ]
        );
        // ---------------------------------------

        $this->addGrid();

        return parent::_prepareLayout();
    }

    private function addGrid(): void
    {
        switch ($this->viewMode) {
            case \M2E\Temu\Block\Adminhtml\Listing\View\Switcher::VIEW_MODE_CHANNEL:
                $gridClass = \M2E\Temu\Block\Adminhtml\Listing\View\Temu\Grid::class;
                break;
            case \M2E\Temu\Block\Adminhtml\Listing\View\Switcher::VIEW_MODE_MAGENTO:
                $gridClass = \M2E\Temu\Block\Adminhtml\Listing\View\Magento\Grid::class;
                break;
            case \M2E\Temu\Block\Adminhtml\Listing\View\Switcher::VIEW_MODE_SETTINGS:
                $gridClass = \M2E\Temu\Block\Adminhtml\Listing\View\Settings\Grid::class;
                break;
            default:
                throw new \M2E\Temu\Model\Exception\Logic(sprintf('Unknown view mode - %s', $this->viewMode));
        }

        $this->addChild('grid', $gridClass);
    }

    protected function _toHtml(): string
    {
        return '<div id="listing_view_progress_bar"></div>' .
            '<div id="listing_container_errors_summary" class="errors_summary" style="display: none;"></div>' .
            '<div id="listing_view_content_container">' .
            parent::_toHtml() .
            '</div>';
    }

    public function getGridHtml()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            return parent::getGridHtml();
        }

        $this->jsUrl->add(
            $this->getUrl('*/listing_variation_product_manage/index'),
            'variationProductManageOpenPopupUrl'
        );

        $this->jsTranslator->addTranslations(
            [
                'Add New Rule' => __('Add New Rule'),
                'Add/Edit Categories Rule' => __('Add/Edit Categories Rule'),
                'Based on Magento Categories' => __('Based on Magento Categories'),
                'Rule with the same Title already exists.' => __('Rule with the same Title already exists.'),
                'Compatibility Attribute' => __('Compatibility Attribute'),
                'Sell on Another Marketplace' => __('Sell on Another Shop'),
                'Create new' => __('Create new'),
                'Linking Product' => __('Linking Product'),
            ]
        );

        return parent::getGridHtml();
    }

    private function getSettingsButtonDropDownItems(): array
    {
        $items = [];

        $backUrl = $this->urlHelper->makeBackUrlParam(
            '*/listing/view',
            ['id' => $this->uiListingRuntimeStorage->getListing()->getId()]
        );

        $url = $this->getUrl(
            '*/listing/edit',
            [
                'id' => $this->uiListingRuntimeStorage->getListing()->getId(),
                'back' => $backUrl,
            ]
        );
        $items[] = [
            'label' => __('Configuration'),
            'onclick' => 'window.open(\'' . $url . '\',\'_blank\');',
            'default' => true,
        ];

        return $items;
    }
}
