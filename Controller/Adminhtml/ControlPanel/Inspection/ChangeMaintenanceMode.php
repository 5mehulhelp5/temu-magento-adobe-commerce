<?php

namespace M2E\Temu\Controller\Adminhtml\ControlPanel\Inspection;

use M2E\Temu\Controller\Adminhtml\ControlPanel\AbstractMain;

class ChangeMaintenanceMode extends AbstractMain
{
    private \M2E\Temu\Helper\View\ControlPanel $controlPanelHelper;
    private \M2E\Temu\Helper\Module\Maintenance $maintenanceHelper;

    public function __construct(
        \M2E\Temu\Helper\View\ControlPanel $controlPanelHelper,
        \M2E\Temu\Helper\Module\Maintenance $maintenanceHelper
    ) {
        parent::__construct();
        $this->controlPanelHelper = $controlPanelHelper;
        $this->maintenanceHelper = $maintenanceHelper;
    }

    public function execute()
    {
        if ($this->maintenanceHelper->isEnabled()) {
            $this->maintenanceHelper->disable();
        } else {
            $this->maintenanceHelper->enable();
        }

        $this->messageManager->addSuccess(__('Changed.'));

        return $this->_redirect($this->controlPanelHelper->getPageUrl());
    }
}
