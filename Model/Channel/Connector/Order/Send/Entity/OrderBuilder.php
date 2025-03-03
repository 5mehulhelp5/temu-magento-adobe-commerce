<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Order\Send\Entity;

class OrderBuilder
{
    private string $channelOrderId;
    private array $orderItems = [];

    public static function create(): self
    {
        return new self();
    }

    public function build(): Order
    {
        $this->validate();

        return new Order(
            $this->channelOrderId,
            $this->orderItems,
        );
    }

    public function setOrderId(string $channelOrderId): self
    {
        $this->channelOrderId = $channelOrderId;

        return $this;
    }

    /**
     * @param \M2E\Temu\Model\Order\Item[] $orderItems
     *
     * @return $this
     */
    public function setItems(array $orderItems): self
    {
        $result = [];
        foreach ($orderItems as $orderItem) {
            /** @var \M2E\Temu\Model\Order\Item $orderItem */
            $result[] = [
                'id' => $orderItem->getChanelOrderItemId(),
                'qty' => $orderItem->getQty()
            ];
        }

        $this->orderItems = $result;

        return $this;
    }

    private function validate(): void
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->channelOrderId)) {
            throw new \M2E\Temu\Model\Exception\Logic('Temu order ID not set');
        }

        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (empty($this->orderItems)) {
            throw new \M2E\Temu\Model\Exception\Logic('Temu order items info is empty');
        }
    }
}
