<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Account\GetAuthInfo;

use M2E\Temu\Model\Channel\Connector\Account\GetAuthInfoCommand;

class Processor
{
    private \M2E\Temu\Model\Connector\Client\Single $client;

    public function __construct(\M2E\Temu\Model\Connector\Client\Single $client)
    {
        $this->client = $client;
    }

    /**
     * @param \M2E\Temu\Model\Account[] $accounts
     *
     * @return \M2E\Temu\Model\Channel\Account\AuthInfoCollection
     */
    public function get(array $accounts): \M2E\Temu\Model\Channel\Account\AuthInfoCollection
    {
        $hashes = [];
        foreach ($accounts as $account) {
            $hashes[] = $account->getServerHash();
        }

        $command = new GetAuthInfoCommand($hashes);
        /** @var \M2E\Temu\Model\Channel\Account\AuthInfoCollection $response */
        $response = $this->client->process($command);

        return $response;
    }
}
