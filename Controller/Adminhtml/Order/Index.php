<?php

namespace M2E\Temu\Controller\Adminhtml\Order;

class Index extends AbstractOrder
{
    public function execute()
    {
        $this->init();
        $this->addContent($this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Order\Order::class));
        $this->setPageHelpLink('https://docs-m2.m2epro.com/docs/m2e-temu-orders/');

        return $this->getResultPage();
    }
}
