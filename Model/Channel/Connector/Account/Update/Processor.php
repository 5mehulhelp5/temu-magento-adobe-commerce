<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Account\Update;

class Processor
{
    private \M2E\Temu\Model\Connector\Client\Single $serverClient;

    private const SERVER_CHANGE_MODE_ERROR_CODE = 1400;

    public function __construct(\M2E\Temu\Model\Connector\Client\Single $serverClient)
    {
        $this->serverClient = $serverClient;
    }

    /**
     * @param \M2E\Temu\Model\Account $account
     * @param string $consumerKey
     * @param string $secretKey
     *
     * @return \M2E\Temu\Model\Channel\Account
     * @throws \M2E\Core\Model\Exception
     * @throws \M2E\Core\Model\Exception\Connection
     * @throws \M2E\Temu\Model\Exception\UnableAccountUpdate
     */
    public function process(
        \M2E\Temu\Model\Account $account,
        string $authCode
    ): \M2E\Temu\Model\Channel\Account {
        $command = new \M2E\Temu\Model\Channel\Connector\Account\UpdateCommand(
            $account->getServerHash(),
            $authCode
        );

        /** @var \M2E\Temu\Model\Channel\Account */
        return $this->serverClient->process($command);
    }
}
