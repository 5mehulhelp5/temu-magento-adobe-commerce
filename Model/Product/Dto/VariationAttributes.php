<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Dto;

class VariationAttributes
{
    private array $items = [];

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function addItem(VariationAttributeItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return array<VariationAttributeItem>
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
            $this->addItem(new VariationAttributeItem(
                $item['attribute_code'],
                $item['name'],
            ));
        }

        return $this;
    }

    public function exportToJson(): ?string
    {
        $items = array_map(function (VariationAttributeItem $item) {
            return [
                'attribute_code' => $item->getAttributeCode(),
                'name' => $item->getName(),
            ];
        }, $this->items);

        if (empty($items)) {
            return null;
        }

        return json_encode($items, JSON_THROW_ON_ERROR);
    }
}
