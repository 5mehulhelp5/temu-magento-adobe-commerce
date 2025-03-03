<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\ControlPanel\Inspection;

use M2E\Temu\Controller\Adminhtml\ControlPanel\AbstractMain;

class SetMagentoCoreSetupValue extends AbstractMain
{
    private \Magento\Framework\Module\ModuleResource $moduleResource;
    private \M2E\Temu\Helper\View\ControlPanel $controlPanelHelper;
    private \M2E\Temu\Setup\UpgradeCollection $updateCollection;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $dbContext,
        \M2E\Temu\Helper\View\ControlPanel $controlPanelHelper,
        \M2E\Temu\Setup\UpgradeCollection $updateCollection
    ) {
        parent::__construct();
        $this->moduleResource = new \Magento\Framework\Module\ModuleResource($dbContext);
        $this->controlPanelHelper = $controlPanelHelper;
        $this->updateCollection = $updateCollection;
    }

    public function execute()
    {
        $version = $this->getRequest()->getParam('version');
        if (!$version) {
            $this->messageManager->addWarning('Version is not provided.');

            return $this->_redirect($this->controlPanelHelper->getPageUrl());
        }

        $version = str_replace(',', '.', $version);
        if (!version_compare($this->updateCollection->getMinAllowedVersion(), $version, '<=')) {
            $this->messageManager->addError(
                sprintf(
                    'Extension upgrade can work only from %s version.',
                    $this->updateCollection->getMinAllowedVersion()
                )
            );

            return $this->_redirect($this->controlPanelHelper->getPageUrl());
        }

        $this->moduleResource->setDbVersion(\M2E\Temu\Helper\Module::IDENTIFIER, $version);
        $this->moduleResource->setDataVersion(\M2E\Temu\Helper\Module::IDENTIFIER, $version);

        $this->messageManager->addSuccess(__('Extension upgrade was completed.'));

        return $this->_redirect($this->controlPanelHelper->getPageUrl());
    }
}
