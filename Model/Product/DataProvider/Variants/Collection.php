<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Variants;

class Collection
{
    /** @var Item[] */
    public array $items = [];

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
            $images = $item->getImages();
            sort($images);
            $onlineData[$item->getSku()] = [
                'online_sku' => $item->getSku(),
                'online_price' => $item->getPrice(),
                'online_qty' => $item->getQty(),
                'images' => \M2E\Core\Helper\Data::md5String(json_encode($images)),
                'variation_attributes' => $item->getVariationAttributes(),
                'package_weight' => $item->getPackageWeight(),
                'package_dimensions' => $item->getPackageDimensions(),
            ];
        }

        return $onlineData;
    }
}
