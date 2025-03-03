<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Magento\Order\Shipment;

class PrepareShipmentItems
{
    public function getQtyToShip(\M2E\Temu\Model\Order $channelOrder, $itemsToShip): array
    {
        $dispatchedQtyMap = [];
        /** @var \M2E\Temu\Model\Order\Item $channelItem */
        foreach ($channelOrder->getItems() as $channelItem) {
            $magentoProduct = $channelItem->getMagentoProduct();
            if (!$magentoProduct || !$magentoProduct->getProductId()) {
                continue;
            }
            if ($channelItem->getQty() > 0) {
                $dispatchedQtyMap[$magentoProduct->getProductId()] = $channelItem->getQty();
            }
        }

        $itemsQtyToShip = [];
        foreach ($itemsToShip as $magentoOrderItem) {
            $productId = (int)$magentoOrderItem->getProductId();
            if (isset($dispatchedQtyMap[$productId])) {
                $magentoAllowQty = (int)$magentoOrderItem->getQtyToShip();
                /**
                 * @psalm-suppress RedundantCast
                 */
                $qty = (int)$dispatchedQtyMap[$productId];

                if ($qty > $magentoAllowQty) {
                    $qty = $magentoAllowQty;
                }

                if ($qty === 0) {
                    continue;
                }

                $itemsQtyToShip[$productId] = $qty;
            }
        }

        return $itemsQtyToShip;
    }
}
