<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Product;

class BulkStopCommand implements \M2E\Core\Model\Connector\CommandInterface
{
    private string $accountHash;
    private array $requestData;

    public function __construct(
        string $accountHash,
        array $requestData
    ) {
        $this->accountHash = $accountHash;
        $this->requestData = $requestData;
    }

    public function getCommand(): array
    {
        return ['product', 'stop', 'entities'];
    }

    public function getRequestData(): array
    {
        return [
            'account' => $this->accountHash,
            'product_ids' => $this->requestData,
        ];
    }

    public function parseResponse(
        \M2E\Core\Model\Connector\Response $response
    ): \M2E\Core\Model\Connector\Response {
        return $response;
    }
}
