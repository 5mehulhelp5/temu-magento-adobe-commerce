<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Account;

class UpdateCredentials extends \M2E\Temu\Controller\Adminhtml\AbstractAccount
{
    private \M2E\Temu\Helper\Module\Exception $helperException;
    private \M2E\Temu\Model\Account\Update $accountUpdate;
    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Model\Account\Ui\UrlHelper $accountUrlHelper;

    public function __construct(
        \M2E\Temu\Model\Account\Update $accountUpdate,
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Helper\Module\Exception $helperException,
        \M2E\Temu\Model\Account\Ui\UrlHelper $accountUrlHelper
    ) {
        parent::__construct();

        $this->helperException = $helperException;
        $this->accountUpdate = $accountUpdate;
        $this->accountRepository = $accountRepository;
        $this->accountUrlHelper = $accountUrlHelper;
    }

    public function execute()
    {
        $accountId = (int)$this->getRequest()->getParam('id', 0);

        if ($accountId === 0) {
            $this->messageManager->addErrorMessage(__('Account does not exist.'));
            $this->setJsonContent(
                [
                    'result' => false,
                    'redirectUrl' => $this->_redirect->getRefererUrl(),
                ]
            );

            return $this->getResult();
        }

        $account = $this->accountRepository->get($accountId);
        $token = $this->getRequest()->getPost('token');

        $resultUrl = $this->accountUrlHelper->getEditUrl($accountId);
        if (empty($token)) {
            $this->messageManager->addErrorMessage(
                __('Please complete all required fields before saving the configurations.')
            );
            $this->setJsonContent(
                [
                    'result' => false,
                    'redirectUrl' => $resultUrl,
                ]
            );

            return $this->getResult();
        }

        try {
            $this->accountUpdate->updateCredentials(
                $account,
                $token
            );
        } catch (\Throwable $exception) {
            $this->helperException->process($exception);

            $message = __(
                'The %channel_title access obtaining is currently unavailable.<br/>Reason: %error_message',
                [
                    'error_message' => $exception->getMessage(),
                    'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                ],
            );

            $this->messageManager->addError($message);
            $this->setJsonContent(
                [
                    'result' => false,
                    'redirectUrl' => $resultUrl,
                ]
            );

            return $this->getResult();
        }

        $this->messageManager->addSuccessMessage(__('Access Token was updated'));
        $this->setJsonContent(
            [
                'result' => true,
                'redirectUrl' => $resultUrl,
            ]
        );

        return $this->getResult();
    }
}
