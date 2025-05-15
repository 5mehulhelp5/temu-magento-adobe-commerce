<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Account\Add;

class Processor
{
    private \M2E\Temu\Model\Connector\Client\Single $serverClient;

    public function __construct(\M2E\Temu\Model\Connector\Client\Single $serverClient)
    {
        $this->serverClient = $serverClient;
    }

    /**
     * @param string $authCode
     * @param string $region
     * @param string|null $referer
     * @param string|null $callbackHost
     *
     * @return \M2E\Temu\Model\Channel\Connector\Account\Add\Response
     * @throws \M2E\Core\Model\Exception
     * @throws \M2E\Core\Model\Exception\Connection
     */
    public function process(
        string $authCode,
        string $region,
        ?string $referer,
        ?string $callbackHost
    ): Response {
        $command = new \M2E\Temu\Model\Channel\Connector\Account\AddCommand(
            $authCode,
            $region,
            $referer,
            $callbackHost
        );

        /** @var Response */
        return $this->serverClient->process($command);
    }
}
