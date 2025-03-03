<?php

namespace M2E\Temu\Block\Adminhtml\Order;

use M2E\Temu\Block\Adminhtml\Magento\AbstractBlock;

class PageActions extends AbstractBlock
{
    private const CONTROLLER_NAME = 'order';

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml(): string
    {
        $accountSwitcherBlock = $this
            ->getLayout()
            ->createBlock(\M2E\Temu\Block\Adminhtml\Account\Switcher::class)
            ->setData(['controller_name' => self::CONTROLLER_NAME]);

        $orderStateSwitcherBlock = $this
            ->getLayout()
            ->createBlock(\M2E\Temu\Block\Adminhtml\Order\NotCreatedFilter::class)
            ->setData(['controller' => self::CONTROLLER_NAME]);

        return
            '<div class="filter_block">'
            . $accountSwitcherBlock->toHtml()
            . $orderStateSwitcherBlock->toHtml()
            . '</div>'
            . parent::_toHtml();
    }
}
