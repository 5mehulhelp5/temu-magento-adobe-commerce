<?php

namespace M2E\Temu\Controller\Adminhtml\Wizard\InstallationTemu;

use M2E\Temu\Controller\Adminhtml\Wizard\InstallationTemu;

class Congratulation extends Installation
{
    public function execute()
    {
        $this->init();

        return $this->congratulationAction();
    }
}
