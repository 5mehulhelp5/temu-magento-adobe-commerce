<?php

namespace M2E\Temu\Block\Adminhtml;

/**
 * Class \M2E\Temu\Block\Adminhtml\Developers
 */
class Developers extends \M2E\Temu\Block\Adminhtml\Magento\AbstractContainer
{
    //########################################

    protected function _construct()
    {
        parent::_construct();

        // Set buttons actions
        // ---------------------------------------
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');
        // ---------------------------------------
    }

    //########################################

    protected function _toHtml()
    {
        return parent::_toHtml() . '<div id="developers_tab_container"></div>';
    }

    //########################################
}
