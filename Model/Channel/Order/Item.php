<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Order;

class Item
{
    private string $id;
    private string $channelProductId; // goods_id
    private ?string $sku;
    private string $skuId;
    private int $qty;
    private string $status;
    private int $qtyCancelledBeforeShipment;
    private int $fulfillmentType;
    private Item\Price $price;
    private ?Item\Shipment $shipment;

    public function __construct(
        string $id,
        string $channelProductId,
        ?string $sku,
        string $skuId,
        string $status,
        int $qty,
        int $qtyCancelledBeforeShipment,
        int $fulfillmentType,
        Item\Price $price,
        ?Item\Shipment $shipment
    ) {
        $this->id = $id;
        $this->channelProductId = $channelProductId;
        $this->sku = $sku;
        $this->skuId = $skuId;
        $this->status = $status;
        $this->qty = $qty;
        $this->qtyCancelledBeforeShipment = $qtyCancelledBeforeShipment;
        $this->fulfillmentType = $fulfillmentType;
        $this->price = $price;
        $this->shipment = $shipment;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getChannelProductId(): string
    {
        return $this->channelProductId;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getSkuId(): string
    {
        return $this->skuId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function getQtyCancelledBeforeShipment(): int
    {
        return $this->qtyCancelledBeforeShipment;
    }

    public function getFulfillmentType(): int
    {
        return $this->fulfillmentType;
    }

    public function getPrice(): Item\Price
    {
        return $this->price;
    }

    public function getTracking(): ?Item\Shipment
    {
        return $this->shipment;
    }
}
