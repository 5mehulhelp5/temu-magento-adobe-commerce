<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\General;

class MsiNotificationPopupClose extends \M2E\Temu\Controller\Adminhtml\AbstractBase
{
    private \M2E\Temu\Model\MSI\Notification\Manager $msiNotificationManager;

    public function __construct(
        \M2E\Temu\Model\MSI\Notification\Manager $msiNotificationManager,
        \M2E\Temu\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->msiNotificationManager = $msiNotificationManager;
    }

    public function execute()
    {
        $this->msiNotificationManager->markAsShow();
        $this->setJsonContent(['status' => true]);

        return $this->getResult();
    }
}
