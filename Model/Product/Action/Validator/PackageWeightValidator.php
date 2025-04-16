<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Validator;

class PackageWeightValidator implements ValidatorInterface
{
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

            if ($value <= 0) {
                return (string)__(
                    'The product package weight is missing or invalid.
                To list the Product, please make sure that the Package settings are correct.'
                );
            }
        } catch (\M2E\Temu\Model\Product\PackageDimension\PackageDimensionException $exception) {
            return $exception->getMessage();
        }

        return null;
    }
}
