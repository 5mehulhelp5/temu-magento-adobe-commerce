<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel;

class ShippingProvider
{
    private int $id;
    private string $name;
    private \M2E\Temu\Model\Channel\Shipping\Region $region;

    public function __construct(
        int $id,
        string $name,
        \M2E\Temu\Model\Channel\Shipping\Region $region
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->region = $region;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRegion(): Shipping\Region
    {
        return $this->region;
    }
}
