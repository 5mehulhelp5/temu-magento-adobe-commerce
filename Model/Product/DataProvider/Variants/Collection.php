<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Variants;

class Collection
{
    /** @var Item[] */
    private array $items = [];

    public function toArray(): array
    {
        return array_map(
            fn(Item $item) => $item->toArray(),
            $this->items
        );
    }

    public function toArrayForList(): array
    {
        return array_map(
            fn(Item $item) => $item->toArrayForList(),
            $this->items
        );
    }

    public function addItem(Item $item)
    {
        $this->items[] = $item;
    }

    public function collectOnlineData(): array
    {
        $onlineData = [];
        foreach ($this->items as $item) {
            $onlineData[$item->getSkuId()] = [
                'sku_id' => $item->getSkuId(),
                'online_price' => $item->getPrice(),
                'online_qty' => $item->getQty(),
            ];
        }

        return $onlineData;
    }

    public function collectOnlineDataForList(): array
    {
        $onlineData = [];
        foreach ($this->items as $item) {
            $onlineData[$item->getSku()] = [
                'online_sku' => $item->getSku(),
                'online_price' => $item->getPrice(),
                'online_qty' => $item->getQty(),
                'images' => \M2E\Core\Helper\Data::md5String(json_encode($item->getImages())),
                'variation_attributes' => $item->getVariationAttributes(),
                'package_weight' => $item->getPackageWeight(),
                'package_dimensions' => $item->getPackageDimensions(),
            ];
        }

        return $onlineData;
    }
}
