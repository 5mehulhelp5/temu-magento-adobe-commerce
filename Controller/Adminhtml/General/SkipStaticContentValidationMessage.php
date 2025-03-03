<?php

namespace M2E\Temu\Controller\Adminhtml\General;

class SkipStaticContentValidationMessage extends \M2E\Temu\Controller\Adminhtml\AbstractGeneral
{
    private \M2E\Temu\Model\Registry\Manager $registry;
    private \M2E\Temu\Model\Module $module;

    public function __construct(
        \M2E\Temu\Model\Registry\Manager $registry,
        \M2E\Temu\Model\Module $module,
        \M2E\Temu\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->module = $module;
        $this->registry = $registry;
    }

    public function execute()
    {
        if ($this->getRequest()->getParam('skip_message', false)) {
            $this->registry->setValue(
                '/global/notification/static_content/skip_for_version/',
                $this->module->getPublicVersion()
            );
        }

        $backUrl = base64_decode($this->getRequest()->getParam('back'));

        return $this->_redirect($backUrl);
    }
}
