<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml;

abstract class AbstractMapping extends \M2E\Temu\Controller\Adminhtml\AbstractMain
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('M2E_Temu::configuration_mapping');
    }
}
