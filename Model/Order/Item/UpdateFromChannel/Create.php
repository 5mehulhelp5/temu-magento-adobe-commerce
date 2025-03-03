<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Item\UpdateFromChannel;

class Create
{
    private \M2E\Temu\Model\Order $order;
    private \M2E\Temu\Model\Channel\Order\Item $channelItem;
    private \M2E\Temu\Model\Order\ItemFactory $itemFactory;
    private \M2E\Temu\Model\Order\Item\Repository $repository;

    public function __construct(
        \M2E\Temu\Model\Order $order,
        \M2E\Temu\Model\Channel\Order\Item $channelItem,
        \M2E\Temu\Model\Order\ItemFactory $itemFactory,
        \M2E\Temu\Model\Order\Item\Repository $repository
    ) {
        $this->order = $order;
        $this->channelItem = $channelItem;
        $this->itemFactory = $itemFactory;
        $this->repository = $repository;
    }

    public function process(): \M2E\Temu\Model\Order\Item
    {
        $item = $this->handleCreate();

        return $item;
    }

    private function handleCreate(): \M2E\Temu\Model\Order\Item
    {
        $item = $this->itemFactory->createFromChannel($this->order, $this->channelItem);
        $this->repository->create($item);

        return $item;
    }
}
