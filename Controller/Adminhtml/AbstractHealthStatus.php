<?php

namespace M2E\Temu\Controller\Adminhtml;

abstract class AbstractHealthStatus extends \M2E\Temu\Controller\Adminhtml\AbstractBase
{
    protected function getLayoutType(): string
    {
        return self::LAYOUT_TWO_COLUMNS;
    }

    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('M2E_Temu::help_center_health_status');
    }

    protected function getMenuRootNodeNick(): string
    {
        return \M2E\Temu\Helper\View\Temu::MENU_ROOT_NODE_NICK;
    }
}
