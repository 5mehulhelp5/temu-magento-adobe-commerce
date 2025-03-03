<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\DataProvider;

use M2E\Temu\Model\Product\DataProvider\DataBuilderHelpTrait;
use M2E\Temu\Model\Product\DataProvider\DataBuilderInterface;

class PriceProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'Price';

    private \M2E\Temu\Model\Product\PriceCalculatorFactory $priceCalculatorFactory;

    public function __construct(\M2E\Temu\Model\Product\PriceCalculatorFactory $priceCalculatorFactory)
    {
        $this->priceCalculatorFactory = $priceCalculatorFactory;
    }

    public function getPrice(\M2E\Temu\Model\Product\VariantSku $variantSku): Price\Value
    {
        $price = $this->getCalculatedPriceWithModifier($variantSku);

        return new Price\Value(
            $price,
            $variantSku->getProduct()->getCurrencyCode()
        );
    }

    private function getCalculatedPriceWithModifier(
        \M2E\Temu\Model\ProductInterface $product
    ): float {
        $src = $product->getSellingFormatTemplate()->getFixedPriceSource();
        $priceModifier = $product->getSellingFormatTemplate()->getFixedPriceModifier();

        $calculator = $this->priceCalculatorFactory->create($product);
        $calculator->setSource($src);
        $calculator->setModifier($priceModifier);

        return (float)$calculator->getProductValue();
    }
}
