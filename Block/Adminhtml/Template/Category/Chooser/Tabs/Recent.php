<?php

namespace M2E\Temu\Block\Adminhtml\Template\Category\Chooser\Tabs;

class Recent extends \M2E\Temu\Block\Adminhtml\Magento\AbstractBlock
{
    public function _construct()
    {
        parent::_construct();

        $this->setId('temuCategoryChooserCategoryRecent');
        $this->setTemplate('template/category/chooser/tabs/recent.phtml');
    }
}
