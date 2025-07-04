<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Account;

use M2E\Temu\Controller\Adminhtml\AbstractAccount;
use M2E\Temu\Model\Account\Issue\ValidTokens;

class AfterGetToken extends AbstractAccount
{
    private \M2E\Temu\Helper\Module\Exception $helperException;
    private \M2E\Temu\Model\Account\Update $accountUpdate;
    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Model\Account\Create $accountCreate;

    public function __construct(
        \M2E\Temu\Model\Account\Create $accountCreate,
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\Account\Update $accountUpdate,
        \M2E\Temu\Helper\Module\Exception $helperException
    ) {
        parent::__construct();

        $this->helperException = $helperException;
        $this->accountUpdate = $accountUpdate;
        $this->accountRepository = $accountRepository;
        $this->accountCreate = $accountCreate;
    }

    // ----------------------------------------

    public function execute()
    {
        $authCode = $this->getRequest()->getParam('code');
        $region = $this->getRequest()->getParam('region');
        $specificEndUrl = $this->getRequest()->getParam('specific_end_url');
        /** @var string|null $referrer */
        $referrer = $this->getRequest()->getParam('referrer');
        /** @var string|null $callbackHost */
        $callbackHost = $this->getRequest()->getParam('callback_host');

        if ($authCode === null) {
            $this->_redirect('*/*/index');
        }

        $accountId = (int)$this->getRequest()->getParam('id');
        try {
            if (empty($accountId)) {
                $account = $this->accountCreate->create($authCode, $region, $referrer, $callbackHost);

                if ($specificEndUrl !== null) {
                    return $this->_redirect($specificEndUrl);
                }

                return $this->_redirect(
                    '*/*/edit',
                    [
                        'id' => $account->getId(),
                        '_current' => true,
                    ],
                );
            }

            $account = $this->accountRepository->find($accountId);
            if ($account === null) {
                throw new \LogicException('Account not found.');
            }

            $this->accountUpdate->updateCredentials($account, $authCode);
        } catch (\Throwable $exception) {
            $this->helperException->process($exception);

            $this->messageManager->addError(
                __(
                    'The Temu access obtaining is currently unavailable.<br/>Reason: %error_message',
                    ['error_message' => $exception->getMessage()],
                ),
            );

            return $this->_redirect('*/account');
        }

        $this->messageManager->addSuccessMessage(__('Auth code was saved'));

        return $this->_redirect('*/*/edit', ['id' => $accountId, '_current' => true]);
    }
}
