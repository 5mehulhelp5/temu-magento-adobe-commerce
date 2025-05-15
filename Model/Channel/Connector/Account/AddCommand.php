<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Account;

class AddCommand implements \M2E\Core\Model\Connector\CommandInterface
{
    private string $authCode;
    private string $region;
    private ?string $referer;
    private ?string $callbackHost;

    public function __construct(
        string $authCode,
        string $region,
        ?string $referer,
        ?string $callbackHost
    ) {
        $this->authCode = $authCode;
        $this->region = $region;
        $this->referer = $referer;
        $this->callbackHost = $callbackHost;
    }

    public function getCommand(): array
    {
        return ['account', 'add', 'entity'];
    }

    public function getRequestData(): array
    {
        return [
            'auth_code' => $this->authCode,
            'region' => $this->region,
            'referer' => $this->referer,
            'callback_host' => $this->callbackHost,
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
