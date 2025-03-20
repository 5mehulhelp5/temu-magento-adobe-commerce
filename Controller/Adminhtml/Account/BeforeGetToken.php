<?php

namespace M2E\Temu\Controller\Adminhtml\Account;

use M2E\Temu\Controller\Adminhtml\AbstractAccount;

class BeforeGetToken extends AbstractAccount
{
    private \M2E\Temu\Helper\Module\Exception $helperException;
    private \M2E\Temu\Model\Channel\Connector\Account\GetGrantAccessUrl\Processor $connectProcessor;
    private \M2E\Temu\Model\Account\Repository $accountRepository;

    public function __construct(
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\Channel\Connector\Account\GetGrantAccessUrl\Processor $connectProcessor,
        \M2E\Temu\Helper\Module\Exception $helperException
    ) {
        parent::__construct();

        $this->helperException = $helperException;
        $this->connectProcessor = $connectProcessor;
        $this->accountRepository = $accountRepository;
    }

    public function execute(): void
    {
        $accountId = (int)$this->getRequest()->getParam('id', 0);
        $region = $this->getRequest()->getParam('region');
        $specificEndUrl = $this->getRequest()->getParam('specific_end_url');

        try {
            $backUrl = $this->getUrl('*/*/afterGetToken', [
                'id' => $accountId,
                'region' => $region,
                'specific_end_url' => $specificEndUrl,
                '_current' => true,
            ]);

            if ($accountId !== 0) {
                $account = $this->accountRepository->get($accountId);
                $response = $this->connectProcessor->processRefreshToken($backUrl, $account);
            } else {
                $response = $this->connectProcessor->processAddAccount($backUrl, $region);
            }
        } catch (\Exception $exception) {
            $this->helperException->process($exception);
            $error = __(
                'The Temu token obtaining is currently unavailable.<br/>Reason: %error_message',
                ['error_message' => $exception->getMessage()]
            );

            $this->messageManager->addError($error);

            $this->_redirect($this->getUrl('*/*/index'));

            return;
        }

        $this->_redirect($response->getUrl());
    }
}
