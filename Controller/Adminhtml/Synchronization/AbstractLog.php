<?php

namespace M2E\Temu\Controller\Adminhtml\Synchronization;

abstract class AbstractLog extends \M2E\Temu\Controller\Adminhtml\AbstractBase
{
    protected function getMenuRootNodeNick(): string
    {
        return \M2E\Temu\Helper\View\Temu::MENU_ROOT_NODE_NICK;
    }

    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('M2E_Temu::help_center_synchronization_log');
    }
}
