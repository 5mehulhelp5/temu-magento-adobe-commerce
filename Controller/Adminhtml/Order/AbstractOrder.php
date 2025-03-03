<?php

namespace M2E\Temu\Controller\Adminhtml\Order;

use M2E\Temu\Controller\Adminhtml\AbstractMain;

abstract class AbstractOrder extends AbstractMain
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('M2E_Temu::sales_orders');
    }

    protected function init()
    {
        $this->addCss('order.css');
        $this->addCss('switcher.css');
        $this->addCss('temu/order/grid.css');

        $this->getResultPage()->getConfig()->getTitle()->prepend(__('Sales'));
        $this->getResultPage()->getConfig()->getTitle()->prepend(__('Orders'));
    }
}
