<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\DataProvider\Package;

class Value
{
    public array $packageWeight;
    public array $packageDimensions;

    public function __construct(
        array $packageWeight,
        array $packageDimensions
    ) {
        $this->packageWeight = $packageWeight;
        $this->packageDimensions = $packageDimensions;
    }
}
