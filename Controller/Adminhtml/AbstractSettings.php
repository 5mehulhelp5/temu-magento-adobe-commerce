<?php

namespace M2E\Temu\Controller\Adminhtml;

abstract class AbstractSettings extends \M2E\Temu\Controller\Adminhtml\AbstractMain
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('M2E_Temu::configuration_settings');
    }
}
