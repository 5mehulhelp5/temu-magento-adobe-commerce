<?php

namespace M2E\Temu\Controller\Adminhtml\Wizard;

abstract class AbstractInstallation extends \M2E\Temu\Controller\Adminhtml\AbstractWizard
{
    protected function getNick(): string
    {
        return \M2E\Temu\Helper\View\Temu::WIZARD_INSTALLATION_NICK;
    }

    protected function init(): void
    {
        $this->getResultPage()
             ->getConfig()
             ->getTitle()
             ->prepend(
                 __(
                     'Configuration of %channel_title Integration',
                     [
                         'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                     ]
                 )
             );
    }

    protected function getCustomViewNick(): string
    {
        return \M2E\Temu\Helper\View\Temu::NICK;
    }

    protected function getMenuRootNodeNick(): string
    {
        return \M2E\Temu\Helper\View\Temu::MENU_ROOT_NODE_NICK;
    }

    protected function getMenuRootNodeLabel(): string
    {
        return \M2E\Temu\Helper\Module::getMenuRootNodeLabel();
    }
}
