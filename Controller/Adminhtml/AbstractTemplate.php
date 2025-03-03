<?php

namespace M2E\Temu\Controller\Adminhtml;

use M2E\Temu\Controller\Adminhtml\AbstractMain;

abstract class AbstractTemplate extends AbstractMain
{
    protected \M2E\Temu\Model\Policy\Manager $templateManager;

    public function __construct(
        \M2E\Temu\Model\Policy\Manager $templateManager
    ) {
        parent::__construct();
        $this->templateManager = $templateManager;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('M2E_Temu::configuration_templates');
    }
}
