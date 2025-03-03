<?php

namespace M2E\Temu\Block\Adminhtml\Listing;

use M2E\Temu\Block\Adminhtml\Magento\AbstractBlock;

/**
 * Class \M2E\Temu\Block\Adminhtml\Listing\Switcher
 */
abstract class Switcher extends AbstractBlock
{
    //########################################

    public function _construct()
    {
        parent::_construct();

        $this->setAddListingUrl('*/listing_create/index');

        $this->setTemplate('M2E_Temu::listing/switcher.phtml');
    }

    //########################################
}
