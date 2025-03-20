<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\PackageDimension;

class Weight
{
    private float $value;
    private string $unit;

    public function __construct(float $value, string $unit)
    {
        $this->value = $value;
        $this->unit = $unit;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }
}
