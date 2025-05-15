<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Account\Issue;

use M2E\Temu\Model\Issue\DataObject as Issue;

class ValidTokens implements \M2E\Temu\Model\Issue\LocatorInterface
{
    public const ACCOUNT_TOKENS_CACHE_KEY = 'temu_account_tokens_validations';

    private \M2E\Temu\Helper\View\Temu $viewHelper;
    private \M2E\Temu\Helper\Data\Cache\Permanent $cache;
    private \M2E\Temu\Model\Issue\DataObjectFactory $issueFactory;
    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Model\Channel\Connector\Account\GetAuthInfo\Processor $getAuthInfoProcessor;

    public function __construct(
        \M2E\Temu\Helper\View\Temu $viewHelper,
        \M2E\Temu\Helper\Data\Cache\Permanent $cache,
        \M2E\Temu\Model\Issue\DataObjectFactory $issueFactory,
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\Channel\Connector\Account\GetAuthInfo\Processor $getAuthInfoProcessor
    ) {
        $this->viewHelper = $viewHelper;
        $this->cache = $cache;
        $this->issueFactory = $issueFactory;
        $this->accountRepository = $accountRepository;
        $this->getAuthInfoProcessor = $getAuthInfoProcessor;
    }

    /**
     * @inheritDoc
     * @throws \M2E\Temu\Model\Exception\Logic
     * @throws \M2E\Temu\Model\Exception
     * @throws \Exception
     */
    public function getIssues(): array
    {
        if (!$this->isNeedProcess()) {
            return [];
        }

        $accounts = $this->cache->getValue(self::ACCOUNT_TOKENS_CACHE_KEY);
        if ($accounts !== null) {
            return $this->prepareIssues($accounts);
        }

        try {
            $accounts = $this->retrieveNotValidAccounts();
        } catch (\Throwable $e) {
            $accounts = [];
        }

        $this->cache->setValue(
            self::ACCOUNT_TOKENS_CACHE_KEY,
            $accounts,
            ['account'],
            3600,
        );

        return $this->prepareIssues($accounts);
    }

    private function isNeedProcess(): bool
    {
        return $this->viewHelper->isInstallationWizardFinished();
    }

    /**
     * @return array
     * @throws \M2E\Temu\Model\Exception
     */
    private function retrieveNotValidAccounts(): array
    {
        $accounts = $this->accountRepository->getAll();
        if (empty($accounts)) {
            return [];
        }

        $authInfoCollection = $this->getAuthInfoProcessor->get($accounts);

        $result = [];
        foreach ($accounts as $account) {
            if (!$authInfoCollection->isValid($account->getServerHash())) {
                $result[] = [
                    'account_name' => $account->getTitle(),
                ];
            }
        }

        return $result;
    }

    private function prepareIssues(array $data): array
    {
        $issues = [];
        foreach ($data as $account) {
            $issues[] = $this->getIssue($account['account_name']);
        }

        return $issues;
    }

    private function getIssue(string $accountName): Issue
    {
        $text = __(
            'The token of %channel_title account "%account_name" is no longer valid.
         Please edit your %channel_title account and get a new token following <a href="%url" target ="_blank">these instructions.</a>',
            [
                'account_name' => $accountName,
                'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                'url' => 'https://help.m2epro.com/en/support/solutions/articles/9000267986-the-token-of-temu-account-is-no-longer-valid-',
            ],
        );

        return $this->issueFactory->createErrorDataObject($accountName, (string)$text, null);
    }
}
