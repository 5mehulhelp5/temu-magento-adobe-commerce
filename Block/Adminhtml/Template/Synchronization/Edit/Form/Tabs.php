<?php

namespace M2E\Temu\Block\Adminhtml\Template\Synchronization\Edit\Form;

use M2E\Temu\Block\Adminhtml\Magento\Tabs\AbstractVerticalTabs;
use M2E\Temu\Block\Adminhtml\Template\Synchronization\Edit\Form\Tabs as SyncTabs;

/**
 * Class \M2E\Temu\Block\Adminhtml\Template\Synchronization\Edit\Form\Tabs
 */
class Tabs extends AbstractVerticalTabs
{
    protected $_template = 'Magento_Backend::widget/tabs.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setDestElementId('tabs_edit_form_data');
    }

    protected function _prepareLayout()
    {
        $this->addTab(
            'revise_rules',
            [
                'label' => __('Revise Rules'),
                'title' => __('Revise Rules'),
                'content' => $this
                    ->getLayout()
                    ->createBlock(\M2E\Temu\Block\Adminhtml\Template\Synchronization\Edit\Form\Tabs\ReviseRules::class)
                    ->toHtml(),
            ]
        );

        $this->addTab(
            'relist_rules',
            [
                'label' => __('Relist Rules'),
                'title' => __('Relist Rules'),
                'content' => $this
                    ->getLayout()
                    ->createBlock(\M2E\Temu\Block\Adminhtml\Template\Synchronization\Edit\Form\Tabs\RelistRules::class)
                    ->toHtml(),
            ]
        );

        $this->addTab(
            'stop_rules',
            [
                'label' => __('Stop Rules'),
                'title' => __('Stop Rules'),
                'content' => $this
                    ->getLayout()
                    ->createBlock(\M2E\Temu\Block\Adminhtml\Template\Synchronization\Edit\Form\Tabs\StopRules::class)
                    ->toHtml(),
            ]
        );

        return parent::_prepareLayout();
    }
}
