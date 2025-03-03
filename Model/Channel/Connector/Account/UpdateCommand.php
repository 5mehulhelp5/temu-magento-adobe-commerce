<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Account;

class UpdateCommand implements \M2E\Core\Model\Connector\CommandInterface
{
    private string $authCode;
    private string $serverHash;

    public function __construct(string $serverHash, string $authCode)
    {
        $this->serverHash = $serverHash;
        $this->authCode = $authCode;
    }

    public function getCommand(): array
    {
        return ['account', 'update', 'entity'];
    }

    public function getRequestData(): array
    {
        return [
            'account' => $this->serverHash,
            'auth_code' => $this->authCode,
        ];
    }

    public function parseResponse(\M2E\Core\Model\Connector\Response $response): \M2E\Temu\Model\Channel\Account
    {
        if ($response->getMessageCollection()->hasErrors()) {
            throw new \M2E\Temu\Model\Exception\UnableAccountUpdate($response->getMessageCollection());
        }

        $responseData = $response->getResponseData();

        return new \M2E\Temu\Model\Channel\Account(
            $responseData['account']['identifier'],
            $responseData['site']['id'],
            $responseData['site']['title']
        );
    }
}
