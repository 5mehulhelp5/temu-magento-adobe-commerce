<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel;

class Order
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_UNSHIPPED = 'unshipped';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCEL = 'cancel';
    public const STATUS_PARTIALLY_SHIPPED = 'partially_shipped';
    public const STATUS_PARTIALLY_DELIVERED = 'partially_delivered';

    private string $orderId;
    private int $siteId;
    private int $regionId;
    private string $status;
    private Order\Price $price;
    private string $currency;
    private Order\Tax $tax;
    private ?Order\Buyer $buyer;
    private ?\DateTimeImmutable $shipByDate;
    private ?\DateTimeImmutable $shippingTime;
    private ?\DateTimeImmutable $deliverByDate;
    private \DateTimeImmutable $createDate;
    private \DateTimeImmutable $updateDate;
    /** @var \M2E\Temu\Model\Channel\Order\Item[] */
    private array $orderItems;
    /** @var \M2E\Temu\Model\Channel\Order\DeliveryAddress */
    private ?Order\DeliveryAddress $deliveryAddress;

    public function __construct(
        string $orderId,
        int $siteId,
        int $regionId,
        string $status,
        string $currency,
        Order\Tax $tax,
        Order\Price $price,
        ?Order\Buyer $buyer,
        ?Order\DeliveryAddress $deliveryAddress,
        ?\DateTimeImmutable $shipByDate,
        ?\DateTimeImmutable $shippingTime,
        ?\DateTimeImmutable $deliverByDate,
        \DateTimeImmutable $createDate,
        \DateTimeImmutable $updateDate,
        array $orderItems
    ) {
        $this->orderId = $orderId;
        $this->siteId = $siteId;
        $this->regionId = $regionId;
        $this->status = $status;
        $this->currency = $currency;
        $this->tax = $tax;
        $this->price = $price;
        $this->buyer = $buyer;
        $this->deliveryAddress = $deliveryAddress;
        $this->shipByDate = $shipByDate;
        $this->shippingTime = $shippingTime;
        $this->deliverByDate = $deliverByDate;
        $this->createDate = $createDate;
        $this->updateDate = $updateDate;
        $this->orderItems = $orderItems;
    }

    /**
     * @return \M2E\Temu\Model\Channel\Order\Item[]
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    // ----------------------------------------

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getSiteId(): int
    {
        return $this->siteId;
    }

    public function getRegionId(): int
    {
        return $this->regionId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPrice(): Order\Price
    {
        return $this->price;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getTaxDetails(): Order\Tax
    {
        return $this->tax;
    }

    public function getBuyer(): ?Order\Buyer
    {
        return $this->buyer;
    }

    public function getDeliveryAddress(): ?Order\DeliveryAddress
    {
        return $this->deliveryAddress;
    }

    public function getShipByDate(): ?\DateTimeImmutable
    {
        return $this->shipByDate;
    }

    public function getShippingTime(): ?\DateTimeImmutable
    {
        return $this->shippingTime;
    }

    public function getDeliverByDate(): ?\DateTimeImmutable
    {
        return $this->deliverByDate;
    }

    public function getCreateDate(): \DateTimeImmutable
    {
        return $this->createDate;
    }

    public function getUpdateDate(): \DateTimeImmutable
    {
        return $this->updateDate;
    }
}
