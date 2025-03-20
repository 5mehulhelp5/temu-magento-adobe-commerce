<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Policy\Shipping\Template;

class GetCommand implements \M2E\Core\Model\Connector\CommandInterface
{
    private string $accountHash;

    public function __construct(string $accountHash)
    {
        $this->accountHash = $accountHash;
    }

    public function getCommand(): array
    {
        return ['shippingTemplate', 'get', 'entities'];
    }

    public function getRequestData(): array
    {
        return [
            'account' => $this->accountHash
        ];
    }

    public function parseResponse(
        \M2E\Core\Model\Connector\Response $response
    ): \M2E\Temu\Model\Channel\Policy\Shipping\Template\Collection {
        $collection = new \M2E\Temu\Model\Channel\Policy\Shipping\Template\Collection();

        foreach ($response->getResponseData()['templates'] ?? [] as $templateData) {
            $collection->add(
                new \M2E\Temu\Model\Channel\Policy\Shipping\Template(
                    $templateData['id'],
                    $templateData['title']
                )
            );
        }

        return $collection;
    }
}
