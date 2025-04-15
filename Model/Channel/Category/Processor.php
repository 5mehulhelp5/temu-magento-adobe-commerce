<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Category;

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
    ): \M2E\Temu\Model\Channel\Connector\Category\Get\Response {
        $command = new \M2E\Temu\Model\Channel\Connector\Category\GetCommand(
            $region,
            $parentId
        );

        /** @var \M2E\Temu\Model\Channel\Connector\Category\Get\Response */
        return $this->serverClient->process($command);
    }
}
