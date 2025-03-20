<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Validator;

class PackageWeightValidator implements ValidatorInterface
{
    public const MAX_WEIGHT = 9999.9;
    private \M2E\Temu\Model\Product\PackageDimensionFinder $packageDimensionFinder;

    public function __construct(
        \M2E\Temu\Model\Product\PackageDimensionFinder $packageDimensionFinder
    ) {
        $this->packageDimensionFinder = $packageDimensionFinder;
    }

    public function validate(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\Configurator $configurator
    ): ?string {
        try {
            $weight = $this->packageDimensionFinder->getWeight($product);
            $value = $weight->getValue();

            if ($value <= 0 || $value > self::MAX_WEIGHT) {
                return sprintf(
                    'The product package weight must be within %s %s.',
                    self::MAX_WEIGHT,
                    $weight->getUnit()
                );
            }
        } catch (\M2E\Temu\Model\Product\PackageDimension\PackageDimensionException $exception) {
            return $exception->getMessage();
        }

        return null;
    }
}
