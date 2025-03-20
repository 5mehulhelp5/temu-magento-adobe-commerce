<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Validator;

class PackageSizeValidator implements ValidatorInterface
{
    public const MAX_SIZE = 999.9;
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
            $size = $this->packageDimensionFinder->getSize($product);

            if (
                min($size->getLength(), $size->getWidth(), $size->getHeight()) <= 0
                || max($size->getLength(), $size->getWidth(), $size->getHeight()) > self::MAX_SIZE
            ) {
                return sprintf(
                    'The product package size must be within %s %s.',
                    self::MAX_SIZE,
                    $size->getUnit()
                );
            }
        } catch (\M2E\Temu\Model\Product\PackageDimension\PackageDimensionException $exception) {
            return $exception->getMessage();
        }

        return null;
    }
}
