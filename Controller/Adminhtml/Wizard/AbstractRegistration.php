<?php

namespace M2E\Temu\Controller\Adminhtml\Wizard;

abstract class AbstractRegistration extends \M2E\Temu\Controller\Adminhtml\AbstractWizard
{
    protected function getCustomViewNick(): string
    {
        return '';
    }

    protected function getNick()
    {
        return null;
    }

    protected function getMenuRootNodeNick()
    {
        return null;
    }

    protected function getMenuRootNodeLabel()
    {
        return null;
    }
}
