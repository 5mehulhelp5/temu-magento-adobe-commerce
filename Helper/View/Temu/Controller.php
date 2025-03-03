<?php

namespace M2E\Temu\Helper\View\Temu;

class Controller
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;
    private \M2E\Temu\Model\Issue\Notification\Channel\Magento\Session $notificationSession;

    public function __construct(
        \M2E\Temu\Model\Issue\Notification\Channel\Magento\Session $notificationSession,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
        $this->notificationSession = $notificationSession;
    }

    public function addMessages(): void
    {
        $issueLocators = [
            \M2E\Temu\Model\Account\Issue\ValidTokens::class,
            \M2E\Temu\Model\Module\Issue\NewVersion::class,
        ];

        foreach ($issueLocators as $locator) {
            /** @var \M2E\Temu\Model\Issue\LocatorInterface $locatorModel */
            $locatorModel = $this->objectManager->create($locator);

            foreach ($locatorModel->getIssues() as $issue) {
                $this->notificationSession->addMessage($issue);
            }
        }
    }
}
