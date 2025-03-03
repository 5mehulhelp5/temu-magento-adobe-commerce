<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Account\Ui;

class UrlHelper
{
    public const PATH_INDEX = 'm2e_temu/account/index';
    public const PATH_CREATE = 'm2e_temu/account/create';
    public const PATH_EDIT = 'm2e_temu/account/edit';
    public const PATH_SAVE = 'm2e_temu/account/save';
    public const PATH_DELETE = 'm2e_temu/account/delete';
    public const PATH_REFRESH = 'm2e_temu/account/refresh';
    public const PATH_UPDATE_CREDENTIALS = 'm2e_temu/account/updateCredentials';
    public const PATH_ACCOUNT_LIST = 'm2e_temu/account/accountList';
    public const PATH_BEFORE_GET_TOKEN = 'm2e_temu/account/beforeGetToken';

    private \Magento\Framework\UrlInterface $urlBuilder;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    public function getIndexUrl(): string
    {
        return $this->urlBuilder->getUrl(self::PATH_INDEX);
    }

    public function getCreateUrl(): string
    {
        return $this->urlBuilder->getUrl(self::PATH_CREATE);
    }

    public function getEditUrl(int $accountId, array $params = []): string
    {
        return $this->urlBuilder->getUrl(self::PATH_EDIT, ['id' => $accountId] + $params);
    }

    public function getUpdateCredentialsUrl(int $accountId): string
    {
        return $this->urlBuilder->getUrl(self::PATH_UPDATE_CREDENTIALS, ['id' => $accountId]);
    }

    public function getSaveUrl(array $params): string
    {
        return $this->urlBuilder->getUrl(self::PATH_SAVE, $params);
    }

    public function getRefreshUrl(int $accountId): string
    {
        return $this->urlBuilder->getUrl(self::PATH_REFRESH, ['id' => $accountId]);
    }

    public function getDeleteUrl(int $accountId): string
    {
        return $this->urlBuilder->getUrl(self::PATH_DELETE, ['id' => $accountId]);
    }

    // ----------------------------------------

    public function getAccountListUrl(): string
    {
        return $this->urlBuilder->getUrl(self::PATH_ACCOUNT_LIST);
    }

    public function getBeforeGetTokenUrl(array $params): string
    {
        return $this->urlBuilder->getUrl(
            self::PATH_BEFORE_GET_TOKEN,
            $params
        );
    }
}
