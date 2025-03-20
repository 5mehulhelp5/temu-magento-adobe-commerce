<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Connector\Category\Get;

class Processor
{
    private \M2E\Temu\Model\Connector\Client\Single $serverClient;

    public function __construct(\M2E\Temu\Model\Connector\Client\Single $serverClient)
    {
        $this->serverClient = $serverClient;
    }

    public function process(
        string $region,
        int $parentId = null
    ): Response {
        $command = new \M2E\Temu\Model\Connector\Category\GetCommand(
            $region,
            $parentId
        );

        /** @var Response */
        return $this->serverClient->process($command);
    }
}
