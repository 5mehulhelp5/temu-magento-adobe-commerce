<?php

namespace M2E\Temu\Controller\Adminhtml;

use M2E\Temu\Helper\Module;
use M2E\Temu\Model\HealthStatus\Task\Result;

abstract class AbstractMain extends AbstractBase
{
    private \M2E\Core\Model\License $license;

    protected function preDispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (($preDispatchResult = parent::preDispatch($request)) !== true) {
            return $preDispatchResult;
        }

        $this->addNotificationMessages();

        if ($request->isGet() && !$request->isPost() && !$request->isXmlHttpRequest()) {
            /** @var \M2E\Temu\Helper\Module\Exception $exceptionHelper */
            $exceptionHelper = $this->_objectManager->get(\M2E\Temu\Helper\Module\Exception::class);
            try {
                $this->_objectManager->get(\M2E\Core\Helper\Client::class)->updateLocationData(false);
            } catch (\Throwable $exception) {
                $exceptionHelper->process($exception);
            }

            try {
                /** @var \M2E\Temu\Model\Servicing\Dispatcher $dispatcher */
                $dispatcher = $this->_objectManager->get(\M2E\Temu\Model\Servicing\Dispatcher::class);
                $dispatcher->processFastTasks();
            } catch (\Throwable $exception) {
                $exceptionHelper->process($exception);
            }
        }

        return true;
    }

    // ----------------------------------------

    protected function initResultPage(): void
    {
        parent::initResultPage();

        if ($this->isContentLocked()) {
            $this->resultPage->getLayout()->unsetChild('page.wrapper', 'page_content');
            $this->resultPage->getLayout()->unsetChild('header', 'header.inner.left');
            $this->resultPage->getLayout()->unsetChild('header', 'header.inner.right');
        }
    }

    // ----------------------------------------

    protected function addLeft(\Magento\Framework\View\Element\AbstractBlock $block)
    {
        if (
            $this->getRequest()->isGet() &&
            !$this->getRequest()->isPost() &&
            !$this->getRequest()->isXmlHttpRequest()
        ) {
            if ($this->isContentLocked()) {
                return $this;
            }
        }

        return parent::addLeft($block);
    }

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock|\Magento\Framework\View\Element\BlockInterface $block
     *
     * @return $this|\M2E\Temu\Controller\Adminhtml\AbstractBase|\Magento\Framework\App\ResponseInterface
     */
    protected function addContent(\Magento\Framework\View\Element\AbstractBlock $block)
    {
        if (
            $this->getRequest()->isGet() &&
            !$this->getRequest()->isPost() &&
            !$this->getRequest()->isXmlHttpRequest()
        ) {
            if ($this->isContentLocked()) {
                return $this;
            }
        }

        if ($this->isContentLockedByWizard()) {
            return $this->getRedirectToWizard();
        }

        return parent::addContent($block);
    }

    // ---------------------------------------

    protected function beforeAddContentEvent()
    {
        $this->appendMSINotificationPopup();

        parent::beforeAddContentEvent();
    }

    protected function appendMSINotificationPopup()
    {
        if (!$this->_objectManager->get(\M2E\Core\Helper\Magento::class)->isMSISupportingVersion()) {
            return;
        }

        /** @var \M2E\Temu\Model\MSI\Notification\Manager $msiNotificationManager */
        $msiNotificationManager = $this->_objectManager->get(\M2E\Temu\Model\MSI\Notification\Manager::class);
        if (!$msiNotificationManager->isNeedShow()) {
            return;
        }

        $block = $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\MsiNotificationPopup::class);
        $this->getLayout()->setChild('js', $block->getNameInLayout(), '');
    }

    protected function getRedirectToWizard()
    {
        /** @var Module\Wizard $wizardHelper */
        $wizardHelper = $this->_objectManager->get(\M2E\Temu\Helper\Module\Wizard::class);
        $activeWizard = $wizardHelper->getActiveBlockerWizard($this->getCustomViewNick());
        $activeWizardNick = $wizardHelper->getNick($activeWizard);

        return $this->_redirect('*/wizard_' . $activeWizardNick, ['referrer' => $this->getCustomViewNick()]);
    }

    // ----------------------------------------

    protected function getCustomViewHelper(): \M2E\Temu\Helper\View\Temu
    {
        return $this->getViewHelper()->getViewHelper();
    }

    protected function getCustomViewControllerHelper(): \M2E\Temu\Helper\View\Temu\Controller
    {
        return $this->getViewHelper()->getControllerHelper();
    }

    protected function getCustomViewNick(): string
    {
        return \M2E\Temu\Helper\View\Temu::NICK;
    }

    private function addNotificationMessages(): void
    {
        if (
            $this->getRequest()->isGet() &&
            !$this->getRequest()->isPost() &&
            !$this->getRequest()->isXmlHttpRequest()
        ) {
            $this->addHealthStatusNotifications();
            $this->addLicenseNotifications();

            if (!$this->addStaticContentNotification()) {
                $this->addStaticContentWarningNotification();
            }

            /** @var \M2E\Temu\Helper\Module $moduleHelper */
            $moduleHelper = $this->_objectManager->get(\M2E\Temu\Helper\Module::class);
            $this->addNotifications($moduleHelper->getUpgradeMessages());

            $this->addCronErrorMessage();
            $this->getCustomViewControllerHelper()->addMessages();
        }
    }

    // ---------------------------------------

    private function addStaticContentNotification(): bool
    {
        /** @var \M2E\Core\Helper\Magento $magentoHelper */
        $magentoHelper = $this->_objectManager->get(\M2E\Core\Helper\Magento::class);
        if (!$magentoHelper->isModeProduction()) {
            return false;
        }

        /** @var \M2E\Temu\Helper\Module $moduleHelper */
        $moduleHelper = $this->_objectManager->get(\M2E\Temu\Helper\Module::class);
        if (!$moduleHelper->isStaticContentDeployed()) {
            $this->addExtendedErrorMessage(
                __(
                    '<p>%extension_title interface cannot work properly and there is no way to ' .
                    'work with it correctly, as your Magento is set to the Production Mode and the static ' .
                    'content data was not deployed.</p>',
                    [
                        'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                    ]
                ),
                self::GLOBAL_MESSAGES_GROUP
            );

            return true;
        }

        return false;
    }

    private function addStaticContentWarningNotification(): void
    {
        /** @var \M2E\Core\Helper\Magento $magentoHelper */
        $magentoHelper = $this->_objectManager->get(\M2E\Core\Helper\Magento::class);
        if (!$magentoHelper->isModeProduction()) {
            return;
        }

        /** @var \M2E\Temu\Model\Module $moduleModel */
        $moduleModel = $this->_objectManager->get(\M2E\Temu\Model\Module::class);
        $skipMessageForVersion = $this->_objectManager->get(\M2E\Temu\Model\Registry\Manager::class)->getValue(
            '/global/notification/static_content/skip_for_version/'
        );

        if (
            $skipMessageForVersion !== null
            && version_compare($skipMessageForVersion, $moduleModel->getPublicVersion(), '==')
        ) {
            return;
        }

        $deployDate = $magentoHelper->getLastStaticContentDeployDate();
        if (!$deployDate) {
            return;
        }

        /** @var \M2E\Core\Model\Setup\Repository $setupResource */
        $setupResource = $this->_objectManager->get(\M2E\Core\Model\Setup\Repository::class);
        $lastUpgrade = $setupResource->findLastUpgrade(\M2E\Temu\Helper\Module::IDENTIFIER);
        if ($lastUpgrade === null) {
            return;
        }

        $lastUpgradeDate = $lastUpgrade->getCreateDate();
        $deployDate = \M2E\Core\Helper\Date::createDateGmt($deployDate);

        if ($deployDate->getTimestamp() > $lastUpgradeDate->modify('- 30 minutes')->getTimestamp()) {
            return;
        }

        $this->addExtendedWarningMessage(
            __(
                '<p>Static content data was not deployed during the last %extension_title ' .
                'installation/upgrade. It may affect some elements of your Magento user interface.</p>' .
                '<p>Please follow <a href="%docs_url" target="_blank">these instructions</a> to deploy ' .
                'static view files.</p><a href="%hide_action_url">Don\'t Show Again</a><br>',
                [
                    'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                    'docs_url' => 'https://devdocs.magento.com/guides/v2.3/config-guide/cli/config-cli-subcommands-static-view.html',
                    'hide_action_url' =>  $this->getUrl(
                        '*/general/skipStaticContentValidationMessage',
                        [
                            'skip_message' => true,
                            'back' => base64_encode($this->getUrl('*/*/*', ['_current' => true])),
                        ]
                    )
                ]
            ),
            self::GLOBAL_MESSAGES_GROUP
        );
    }

    private function addHealthStatusNotifications(): void
    {
        /** @var \M2E\Temu\Model\HealthStatus\CurrentStatus $currentStatus */
        $currentStatus = $this->_objectManager->get(\M2E\Temu\Model\HealthStatus\CurrentStatus::class);
        /** @var \M2E\Temu\Model\HealthStatus\Notification\Settings $notificationSettings */
        $notificationSettings = $this->_objectManager->get(
            \M2E\Temu\Model\HealthStatus\Notification\Settings::class
        );

        if (!$notificationSettings->isModeExtensionPages()) {
            return;
        }

        if ($currentStatus->get() < $notificationSettings->getLevel()) {
            return;
        }

        /** @var \M2E\Temu\Model\HealthStatus\Notification\MessageBuilder $messageBuilder */
        $messageBuilder = $this->_objectManager->get(
            \M2E\Temu\Model\HealthStatus\Notification\MessageBuilder::class
        );

        switch ($currentStatus->get()) {
            case Result::STATE_NOTICE:
                $this->addExtendedNoticeMessage($messageBuilder->build(), self::GLOBAL_MESSAGES_GROUP);
                break;

            case Result::STATE_WARNING:
                $this->addExtendedWarningMessage($messageBuilder->build(), self::GLOBAL_MESSAGES_GROUP);
                break;

            case Result::STATE_CRITICAL:
                $this->addExtendedErrorMessage($messageBuilder->build(), self::GLOBAL_MESSAGES_GROUP);
                break;
        }
    }

    protected function addLicenseNotifications(): void
    {
        $added = false;
        if ($this->getCustomViewHelper()->isInstallationWizardFinished()) {
            $added = $this->addLicenseActivationNotifications();
        }

        if (
            !$added
            && $this->getLicense()->hasKey()
        ) {
            $this->addLicenseValidationFailNotifications();
        }
    }

    // ---------------------------------------

    /**
     * @param array $messages
     */
    private function addNotifications(array $messages): void
    {
        foreach ($messages as $message) {
            if (isset($message['text']) && isset($message['type']) && $message['text'] != '') {
                switch ($message['type']) {
                    case \M2E\Core\Helper\Module::MESSAGE_TYPE_ERROR:
                        $this->getMessageManager()->addError(
                            $this->prepareNotificationMessage($message),
                            self::GLOBAL_MESSAGES_GROUP
                        );
                        break;
                    case \M2E\Core\Helper\Module::MESSAGE_TYPE_WARNING:
                        $this->getMessageManager()->addWarning(
                            $this->prepareNotificationMessage($message),
                            self::GLOBAL_MESSAGES_GROUP
                        );
                        break;
                    case \M2E\Core\Helper\Module::MESSAGE_TYPE_SUCCESS:
                        $this->getMessageManager()->addSuccess(
                            $this->prepareNotificationMessage($message),
                            self::GLOBAL_MESSAGES_GROUP
                        );
                        break;
                    case \M2E\Core\Helper\Module::MESSAGE_TYPE_NOTICE:
                    default:
                        $this->getMessageManager()->addNotice(
                            $this->prepareNotificationMessage($message),
                            self::GLOBAL_MESSAGES_GROUP
                        );
                        break;
                }
            }
        }
    }

    private function prepareNotificationMessage(array $message)
    {
        if (!empty($message['title'])) {
            $title = __($message['title']);
            $text = __($message['text']);

            return "<strong>$title</strong><br/>$text";
        }

        return __($message['text']);
    }

    // ---------------------------------------

    protected function addCronErrorMessage(): void
    {
        /** @var \M2E\Temu\Helper\Module $moduleHelper */
        $moduleHelper = $this->_objectManager->get(\M2E\Temu\Helper\Module::class);
        /** @var \M2E\Temu\Model\Cron\Config $cronConfig */
        $cronConfig = $this->_objectManager->get(\M2E\Temu\Model\Cron\Config::class);

        if (!$cronConfig->isEnabled()) {
            $this->getMessageManager()->addWarning(
                __(
                    'Automatic Synchronization is disabled. You can enable it under ' .
                    '<i>Stores > Settings > Configuration > %extension_title > Module & Channels > ' .
                    'Automatic Synchronization</i>.',
                    [
                        'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                    ]
                ),
                \M2E\Temu\Controller\Adminhtml\AbstractBase::GLOBAL_MESSAGES_GROUP
            );

            return;
        }

        /** @var \M2E\Temu\Model\Cron\Manager $cronManager */
        $cronManager = $this->_objectManager->get(\M2E\Temu\Model\Cron\Manager::class);

        if (
            $moduleHelper->isReadyToWork()
            && $cronManager->isCronLastRunMoreThan(3600)
        ) {
            $message = __(
                'Attention! AUTOMATIC Synchronization is not running at the moment.' .
                ' It does not allow %extension_title to work correctly.' .
                '<br/>Please check this <a href="%url" target="_blank" class="external-link">article</a>' .
                ' for the details on how to resolve the problem.',
                [
                    'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                    'url' => 'https://help.m2epro.com/support/solutions/articles/9000200402',
                ]
            );

            $this->getMessageManager()->addError(
                $message,
                \M2E\Temu\Controller\Adminhtml\AbstractBase::GLOBAL_MESSAGES_GROUP
            );
        }
    }

    // ---------------------------------------

    protected function addLicenseActivationNotifications(): bool
    {
        $license = $this->getLicense();

        if (
            !$license->hasKey()
            || !$license->getInfo()->getDomainIdentifier()->getValidValue()
            || !$license->getInfo()->getIpIdentifier()->getValidValue()
        ) {
            $params = [];
            if ($this->isContentLockedByWizard()) {
                $params['wizard'] = '1';
            }

            /** @var \M2E\Temu\Helper\View\Configuration $configurationHelper */
            $configurationHelper = $this->_objectManager->get(\M2E\Temu\Helper\View\Configuration::class);

            $url = $configurationHelper->getLicenseUrl($params);

            $message = __(
                '%extension_title Module requires activation. Go to the <a href="%url" target ="_blank">License Page</a>.',
                [
                    'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                    'url' => $url,
                ]
            );

            $this->getMessageManager()->addError($message, self::GLOBAL_MESSAGES_GROUP);

            return true;
        }

        return false;
    }

    private function addLicenseValidationFailNotifications(): void
    {
        /** @var \M2E\Temu\Helper\Module\Wizard $wizardHelper */
        $wizardHelper = $this->_objectManager->get(\M2E\Temu\Helper\Module\Wizard::class);
        /** @var \M2E\Temu\Helper\View\Configuration $configurationHelper */
        $configurationHelper = $this->_objectManager->get(\M2E\Temu\Helper\View\Configuration::class);

        $license = $this->getLicense();
        if (!$license->getInfo()->getDomainIdentifier()->isValid()) {
            $params = [];
            if ($wizardHelper->getActiveBlockerWizard($this->getCustomViewNick())) {
                $params['wizard'] = '1';
            }

            $url = $configurationHelper->getLicenseUrl($params);

            $message = __(
                '%extension_title License Key Validation is failed for this Domain. ' .
                'Go to the <a href="%url" target="_blank">License Page</a>.',
                [
                    'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                    'url' => $url,
                ]
            );

            $this->getMessageManager()->addError($message, self::GLOBAL_MESSAGES_GROUP);

            return;
        }

        if (!$license->getInfo()->getIpIdentifier()->isValid()) {
            $params = [];
            if ($wizardHelper->getActiveBlockerWizard($this->getCustomViewNick())) {
                $params['wizard'] = '1';
            }
            $url = $configurationHelper->getLicenseUrl($params);

            $message = __(
                '%extension_title License Key Validation is failed for this IP. ' .
                'Go to the <a href="%url" target="_blank">License Page</a>.',
                [
                    'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                    'url' => $url,
                ]
            );

            $this->getMessageManager()->addError($message, self::GLOBAL_MESSAGES_GROUP);
        }
    }

    // ----------------------------------------

    private function isContentLocked(): bool
    {
        return $this->_objectManager->get(\M2E\Core\Helper\Magento::class)->isModeProduction()
            && !$this->_objectManager->get(\M2E\Temu\Helper\Module::class)->isStaticContentDeployed();
    }

    private function isContentLockedByWizard(): bool
    {
        if ($this->isAjax()) {
            return false;
        }

        /** @var \M2E\Temu\Helper\Module\Wizard $moduleWizardHelper */
        $moduleWizardHelper = $this->_objectManager->get(\M2E\Temu\Helper\Module\Wizard::class);
        $activeWizard = $moduleWizardHelper->getActiveBlockerWizard($this->getCustomViewNick());
        if ($activeWizard === null) {
            return false;
        }

        $nick = $moduleWizardHelper->getNick($activeWizard);
        $activeControllerName = $this->getRequest()->getControllerName();

        if (
            $this->getRequest()->getParam('wizard', false)
            || $activeControllerName === 'wizard_' . $nick
        ) {
            return false;
        }

        return true;
    }

    // ----------------------------------------

    private function getLicense(): \M2E\Core\Model\License
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->license)) {
            return $this->license;
        }

        /** @var \M2E\Core\Model\LicenseService $licenseService */
        $licenseService = $this->_objectManager->get(\M2E\Core\Model\LicenseService::class);

        return $this->license = $licenseService->get();
    }
}
