<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order;

class ItemFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createEmpty(): Item
    {
        return $this->objectManager->create(Item::class);
    }

    public function createFromChannel(
        \M2E\Temu\Model\Order $order,
        \M2E\Temu\Model\Channel\Order\Item $channelItem
    ): Item {
        $obj = $this->createEmpty();

        $obj->create(
            $order,
            $channelItem->getId(),
            $channelItem->getChannelProductId(),
            $channelItem->getSku(),
            $channelItem->getQty()
        );

        $obj->setStatus(self::resolveStatus($channelItem->getStatus()))
            // ----------------------------------------
            ->setSkuId($channelItem->getSkuId())
            // ----------------------------------------
            ->setSalePrice($channelItem->getPrice()->unitRetail)
            ->setBasePrice($channelItem->getPrice()->unitBase)
            // ----------------------------------------
            ->setQtyCancelledBeforeShipment($channelItem->getQtyCancelledBeforeShipment())
            // ----------------------------------------
            ->setFulfillmentType($channelItem->getFulfillmentType())
            // ----------------------------------------
            ->setTrackingDetails(self::createTrackingDetails($channelItem->getTracking()));

        return $obj;
    }

    /**
     * @param \M2E\Temu\Model\Order\Item $item
     * @param \M2E\Temu\Model\Channel\Order\Item $channelItem
     *
     * @return bool - was updated
     */
    public static function updateFromChannel(Item $item, \M2E\Temu\Model\Channel\Order\Item $channelItem): bool
    {
        $wasChanged = false;

        if ($item->getStatus() !== self::resolveStatus($channelItem->getStatus())) {
            $item->setStatus(self::resolveStatus($channelItem->getStatus()));

            $wasChanged = true;
        }

        if ($item->getSkuId() !== $channelItem->getSkuId()) {
            $item->setSkuId($channelItem->getSkuId());

            $wasChanged = true;
        }

        if ($item->getQtyCancelledBeforeShipment() !== $channelItem->getQtyCancelledBeforeShipment()) {
            $item->setQtyCancelledBeforeShipment($channelItem->getQtyCancelledBeforeShipment());

            $wasChanged = true;
        }

        if ($item->getFulfillmentType() !== $channelItem->getFulfillmentType()) {
            $item->setFulfillmentType($channelItem->getFulfillmentType());

            $wasChanged = true;
        }

        if ($item->getSalePrice() !== $channelItem->getPrice()->unitRetail) {
            $item->setSalePrice($channelItem->getPrice()->unitRetail);

            $wasChanged = true;
        }

        if ($item->getBasePrice() !== $channelItem->getPrice()->unitBase) {
            $item->setBasePrice($channelItem->getPrice()->unitBase);

            $wasChanged = true;
        }

        if ($item->getTrackingDetails() !== self::createTrackingDetails($channelItem->getTracking())) {
            $item->setTrackingDetails(self::createTrackingDetails($channelItem->getTracking()));

            $wasChanged = true;
        }

        return $wasChanged;
    }

    // ----------------------------------------

    private static function createTrackingDetails(?\M2E\Temu\Model\Channel\Order\Item\Shipment $tracking): array
    {
        if ($tracking === null) {
            return [];
        }

        return [
            'supplier_name' => $tracking->supplierName,
            'tracking_number' => $tracking->trackingNumber,
        ];
    }

    private static function resolveStatus(string $channelStatus): int
    {
        return \M2E\Temu\Model\Order\StatusResolver::resolve($channelStatus);
    }
}
