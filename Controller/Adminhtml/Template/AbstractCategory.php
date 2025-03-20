<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Template;

abstract class AbstractCategory extends \M2E\Temu\Controller\Adminhtml\AbstractMain
{
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('M2E_Temu::configuration_categories');
    }
}
