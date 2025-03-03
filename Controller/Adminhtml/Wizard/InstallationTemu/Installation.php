<?php

namespace M2E\Temu\Controller\Adminhtml\Wizard\InstallationTemu;

class Installation extends \M2E\Temu\Controller\Adminhtml\Wizard\AbstractInstallation
{
    public function execute()
    {
        return $this->installationAction();
    }
}
