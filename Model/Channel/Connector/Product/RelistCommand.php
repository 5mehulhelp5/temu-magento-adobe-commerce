<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Product;

class RelistCommand implements \M2E\Core\Model\Connector\CommandProcessingInterface
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
        return ['product', 'relist', 'entity'];
    }

    public function getRequestData(): array
    {
        return [
            'account' => $this->accountHash,
            'product' => $this->requestData,
        ];
    }

    public function parseResponse(
        \M2E\Core\Model\Connector\Response $response
    ): \M2E\Core\Model\Connector\Response\Processing {
        return new \M2E\Core\Model\Connector\Response\Processing($response->getResponseData()['processing_id']);
    }
}
