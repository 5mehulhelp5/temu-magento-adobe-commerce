<?php

namespace M2E\Temu\Controller\Adminhtml;

abstract class AbstractGeneral extends \M2E\Temu\Controller\Adminhtml\AbstractBase
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('M2E_Temu::temu');
    }
}
