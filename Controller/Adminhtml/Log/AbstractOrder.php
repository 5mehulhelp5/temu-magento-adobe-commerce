<?php

namespace M2E\Temu\Controller\Adminhtml\Log;

abstract class AbstractOrder extends \M2E\Temu\Controller\Adminhtml\AbstractMain
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('M2E_Temu::sales_logs');
    }
}
