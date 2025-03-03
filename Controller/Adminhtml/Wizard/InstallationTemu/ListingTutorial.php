<?php

namespace M2E\Temu\Controller\Adminhtml\Wizard\InstallationTemu;

class ListingTutorial extends Installation
{
    public function execute()
    {
        $this->init();

        return $this->renderSimpleStep();
    }
}
