<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Account;

class ShippingMapping
{
    private array $shippingMappings;

    /**
     * @param array<int, array<string, string>> $shippingMappings
     *     Key - region ID
     *     value - an array where:
     *         key - magento carrier code
     *         value - temu shipping provider id
     */
    public function __construct(array $shippingMappings = [])
    {
        $this->shippingMappings = $shippingMappings;
    }

    public function isConfigured(): bool
    {
        return !empty($this->shippingMappings);
    }

    public function getProviderIdByCarrierCodeAndRegionId(int $regionId, string $carrierCode): ?int
    {
        return isset($this->shippingMappings[$regionId][$carrierCode])
            ? (int) $this->shippingMappings[$regionId][$carrierCode]
            : null;
    }

    public function getDefaultProviderId(int $regionId): ?int
    {
        return isset($this->shippingMappings[$regionId]['default'])
            ? (int) $this->shippingMappings[$regionId]['default']
            : null;
    }

    public function toArray(): array
    {
        return $this->shippingMappings;
    }
}
