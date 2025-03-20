<?php

namespace M2E\Temu\Model\Connector\Attribute\Get;

class Processor
{
    private \M2E\Temu\Model\Connector\Client\Single $serverClient;

    public function __construct(\M2E\Temu\Model\Connector\Client\Single $serverClient)
    {
        $this->serverClient = $serverClient;
    }

    public function process(
        string $region,
        int $categoryId
    ): Response {
        $command = new \M2E\Temu\Model\Connector\Attribute\GetCommand(
            $region,
            $categoryId
        );

        /** @var Response */
        return $this->serverClient->process($command);
    }
}
