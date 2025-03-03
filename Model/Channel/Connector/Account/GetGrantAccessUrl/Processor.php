<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Account\GetGrantAccessUrl;

class Processor
{
    private \M2E\Temu\Model\Connector\Client\Single $serverClient;

    public function __construct(\M2E\Temu\Model\Connector\Client\Single $serverClient)
    {
        $this->serverClient = $serverClient;
    }

    public function processAddAccount(string $backUrl, string $region): Response
    {
        $command = new \M2E\Temu\Model\Channel\Connector\Account\GetGrantAccessUrlCommand(
            $backUrl,
            $region,
            null
        );

        /** @var Response */
        return $this->serverClient->process($command);
    }

    public function processRefreshToken(string $backUrl, \M2E\Temu\Model\Account $account): Response
    {
        $command = new \M2E\Temu\Model\Channel\Connector\Account\GetGrantAccessUrlCommand(
            $backUrl,
            null,
            $account
        );

        /** @var Response */
        return $this->serverClient->process($command);
    }
}
