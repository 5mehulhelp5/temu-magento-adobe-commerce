<?php

namespace M2E\Temu\Controller\Adminhtml;

use M2E\Temu\Controller\Adminhtml\AbstractMain;

abstract class AbstractAccount extends AbstractMain
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('M2E_Temu::configuration_accounts');
    }
}
