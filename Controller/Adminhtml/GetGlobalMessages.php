<?php

namespace M2E\Temu\Controller\Adminhtml;

use M2E\Temu\Controller\Adminhtml\AbstractMain;

class GetGlobalMessages extends AbstractMain
{
    public function execute()
    {
        if ($this->getCustomViewHelper()->isInstallationWizardFinished()) {
            $this->addLicenseNotifications();
        }

        $this->addCronErrorMessage();
        $this->getCustomViewControllerHelper()->addMessages();

        $messages = $this->getMessageManager()->getMessages(
            true,
            \M2E\Temu\Controller\Adminhtml\AbstractBase::GLOBAL_MESSAGES_GROUP,
        )->getItems();

        foreach ($messages as &$message) {
            $message = [$message->getType() => $message->getText()];
        }

        $this->setJsonContent($messages);

        return $this->getResult();
    }
}
