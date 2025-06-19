<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\Dto;

class VariationData
{
    /** @var array<\M2E\Temu\Model\Product\VariantSku\Dto\VariationDataItem> */
    private array $items = [];

    public function add(VariationDataItem $variationDataItem): void
    {
        $this->items[] = $variationDataItem;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * @return array<\M2E\Temu\Model\Product\VariantSku\Dto\VariationDataItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function importFromJson(string $json): self
    {
        if (empty($json)) {
            return $this;
        }

        $items = json_decode($json, true);

        foreach ($items as $item) {
            $this->add(new VariationDataItem(
                $item['attribute_code'],
                $item['attribute_name'],
                $item['value'],
            ));
        }

        return $this;
    }

    public function exportToJson(): ?string
    {
        $variationData = array_map(function ($item) {
            return [
                'attribute_code' => $item->getAttributeCode(),
                'attribute_name' => $item->getAttributeName(),
                'value' => $item->getValue(),
            ];
        }, $this->items);

        if (empty($variationData)) {
            return null;
        }

        return json_encode($variationData);
    }
}
