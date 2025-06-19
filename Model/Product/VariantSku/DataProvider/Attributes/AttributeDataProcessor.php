<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\DataProvider\Attributes;

class AttributeDataProcessor
{
    /**
     * @return \M2E\Temu\Model\Product\VariantSku\DataProvider\Attributes\Item[]
     */
    public function getAttributes(\M2E\Temu\Model\Product\VariantSku $variantSku): array
    {
        return array_map(function (\M2E\Temu\Model\Product\VariantSku\Dto\VariationDataItem $item) {
            return new \M2E\Temu\Model\Product\VariantSku\DataProvider\Attributes\Item(
                null,
                $item->getValue(),
                $item->getAttributeName(),
                null,
                null
            );
        }, $variantSku->getVariationData()->getItems());
    }

    public function getWarningMessages(): array
    {
        return [];
    }
}
