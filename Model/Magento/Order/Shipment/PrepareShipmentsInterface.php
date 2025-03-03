<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Magento\Order\Shipment;

interface PrepareShipmentsInterface
{
    /**
     * @param \Magento\Sales\Model\Order $magentoOrder
     * @param \M2E\Temu\Model\Order $channelOrder
     * @param \Magento\Sales\Model\Order\Item[] $itemsToShip
     *
     * @return \Magento\Sales\Model\Order\Shipment[]
     */
    public function prepareShipments(
        \Magento\Sales\Model\Order $magentoOrder,
        \M2E\Temu\Model\Order $channelOrder,
        array $itemsToShip
    ): array;
}
