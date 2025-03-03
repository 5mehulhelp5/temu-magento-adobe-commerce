<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Shipping;

use M2E\Temu\Model\Channel\ShippingProvider;

class GetProviders implements \M2E\Core\Model\Connector\CommandInterface
{
    private string $accountHash;

    public function __construct(string $accountHash)
    {
        $this->accountHash = $accountHash;
    }

    public function getCommand(): array
    {
        return ['shipping', 'get', 'providers'];
    }

    public function getRequestData(): array
    {
        return [
            'account' => $this->accountHash
        ];
    }

    public function parseResponse(\M2E\Core\Model\Connector\Response $response): object
    {
        $data = $response->getResponseData();

        $shippingProviders = [];

        if (isset($data['providers'])) {
            foreach ($data['providers'] as $shippingProviderData) {
                $shippingProviders[] = new ShippingProvider(
                    (int)$shippingProviderData['id'],
                    $shippingProviderData['name'],
                    new \M2E\Temu\Model\Channel\Shipping\Region(
                        (int)$shippingProviderData['region']['id'],
                        $shippingProviderData['region']['name']
                    ),
                );
            }
        }

        return new \M2E\Temu\Model\Channel\Connector\Shipping\GetProviders\Response(
            $shippingProviders,
            $response->getMessageCollection()
        );
    }
}
