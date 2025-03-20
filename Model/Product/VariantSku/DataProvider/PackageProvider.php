<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\DataProvider;

use M2E\Temu\Model\Product\DataProvider\DataBuilderHelpTrait;
use M2E\Temu\Model\Product\DataProvider\DataBuilderInterface;

class PackageProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'Package';
    private \M2E\Temu\Model\Product\PackageDimensionFinder $packageDimensionFinder;

    public function __construct(
        \M2E\Temu\Model\Product\PackageDimensionFinder $packageDimensionFinder
    ) {
        $this->packageDimensionFinder = $packageDimensionFinder;
    }

    public function getPackage(\M2E\Temu\Model\Product\VariantSku $variantSku): Package\Value
    {
        $weight = $this->packageDimensionFinder->getWeight($variantSku->getProduct());
        $size = $this->packageDimensionFinder->getSize($variantSku->getProduct());

        $packageWeight = [
            "value" => $weight->getValue(),
            "unit" => $weight->getUnit(),
        ];

        $packageDimensions = [
            "length" => $size->getLength(),
            "width" => $size->getWidth(),
            "height" => $size->getHeight(),
            "unit" => $size->getUnit(),
        ];

        return new Package\Value(
            $packageWeight,
            $packageDimensions
        );
    }
}
