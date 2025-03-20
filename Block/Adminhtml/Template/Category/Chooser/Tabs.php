<?php

namespace M2E\Temu\Block\Adminhtml\Template\Category\Chooser;

class Tabs extends \M2E\Temu\Block\Adminhtml\Magento\Tabs\AbstractHorizontalTabs
{
    private const TAB_ID_RECENT = 'recent';
    private const TAB_ID_BROWSE = 'browse';
    private const TAB_ID_SEARCH = 'search';

    public function _construct()
    {
        parent::_construct();
        $this->setId('temuTemplateCategoryChooserTabs');
        $this->setDestElementId('chooser_tabs_container');
    }

    protected function _prepareLayout()
    {
        $recentContent = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Template\Category\Chooser\Tabs\Recent::class,
        )->toHtml();
        $this->addTab(self::TAB_ID_RECENT, [
            'label' => __('Saved Categories'),
            'title' => __('Saved Categories'),
            'content' => $recentContent,
            'active' => true,
        ]);

        $browseContent = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Template\Category\Chooser\Tabs\Browse::class,
        )->toHtml();
        $this->addTab(self::TAB_ID_BROWSE, [
            'label' => __('Browse'),
            'title' => __('Browse'),
            'content' => $browseContent,
            'active' => false,
        ]);

        $searchContent = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Category\Chooser\Tab\Search::class,
        )->toHtml();
        $this->addTab(self::TAB_ID_SEARCH, [
            'label' => __('Search'),
            'title' => __('Search'),
            'content' => $searchContent,
            'active' => false,
        ]);

        return parent::_prepareLayout();
    }
}
