<?php

namespace M2E\Temu\Block\Adminhtml\Synchronization;

use M2E\Temu\Block\Adminhtml\Magento\Grid\AbstractContainer;

class Log extends AbstractContainer
{
    public function _construct()
    {
        parent::_construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('synchronizationLog');
        $this->_controller = 'adminhtml_synchronization_log';
        // ---------------------------------------

        // Set header text
        // ---------------------------------------
        $this->_headerText = '';
        // ---------------------------------------

        // Set buttons actions
        // ---------------------------------------
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');

        // Set template
        // ---------------------------------------
        $this->setTemplate('M2E_Temu::magento/grid/container/only_content.phtml');
        // ---------------------------------------
    }

    protected function _toHtml()
    {
        $helpBlock = $this
            ->getLayout()
            ->createBlock(
                \M2E\Temu\Block\Adminhtml\HelpBlock::class,
                '',
                [
                    'data' => [
                        'content' => __(
                            'The Log includes information about synchronization ' .
                            'of %extension_title Listings, Orders, Unmanaged Listings.',
                            [
                                'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                            ]
                        ),
                    ],
                ]
            );

        return $helpBlock->toHtml() . parent::_toHtml();
    }
}
