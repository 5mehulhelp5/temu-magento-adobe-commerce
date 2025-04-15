<?php

namespace M2E\Temu\Model\Channel\Attribute;

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
    ): \M2E\Temu\Model\Channel\Connector\Attribute\Get\Response {
        $command = new \M2E\Temu\Model\Channel\Connector\Attribute\GetCommand(
            $region,
            $categoryId
        );

        /** @var \M2E\Temu\Model\Channel\Connector\Attribute\Get\Response */
        return $this->serverClient->process($command);
    }
}
