<?php

namespace M2E\Temu\Controller\Adminhtml\Wizard\InstallationTemu;

use M2E\Temu\Controller\Adminhtml\Wizard\InstallationTemu;

class SetStep extends Installation
{
    public function execute()
    {
        return $this->setStepAction();
    }
}
