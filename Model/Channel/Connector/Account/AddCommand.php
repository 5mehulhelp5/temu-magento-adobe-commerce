<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Account;

class AddCommand implements \M2E\Core\Model\Connector\CommandInterface
{
    private string $authCode;

    private string $region;

    public function __construct(string $authCode, string $region)
    {
        $this->authCode = $authCode;
        $this->region = $region;
    }

    public function getCommand(): array
    {
        return ['account', 'add', 'entity'];
    }

    public function getRequestData(): array
    {
        return [
            'auth_code' => $this->authCode,
            'title' => $this->authCode,
            'region' => $this->region,
        ];
    }

    public function parseResponse(
        \M2E\Core\Model\Connector\Response $response
    ): \M2E\Temu\Model\Channel\Connector\Account\Add\Response {
        if ($response->getMessageCollection()->hasErrors()) {
            throw new \M2E\Temu\Model\Exception\UnableAccountCreate($response->getMessageCollection());
        }

        $responseData = $response->getResponseData();

        return new \M2E\Temu\Model\Channel\Connector\Account\Add\Response(
            $responseData['hash'],
            new \M2E\Temu\Model\Channel\Account(
                $responseData['account']['identifier'],
                $responseData['site']['id'],
                $responseData['site']['title']
            )
        );
    }
}
