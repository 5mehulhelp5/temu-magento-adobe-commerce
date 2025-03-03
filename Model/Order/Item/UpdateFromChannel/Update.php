<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Item\UpdateFromChannel;

class Update
{
    private \M2E\Temu\Model\Order\Item $item;
    private \M2E\Temu\Model\Channel\Order\Item $channelItem;
    private \M2E\Temu\Model\Order\Item\Repository $repository;

    public function __construct(
        \M2E\Temu\Model\Order\Item $item,
        \M2E\Temu\Model\Channel\Order\Item $channelItem,
        \M2E\Temu\Model\Order\Item\Repository $repository
    ) {
        $this->item = $item;
        $this->channelItem = $channelItem;
        $this->repository = $repository;
    }

    public function process(): void
    {
        $wasChanged = \M2E\Temu\Model\Order\ItemFactory::updateFromChannel($this->item, $this->channelItem);
        if (!$wasChanged) {
            return;
        }

        $this->repository->save($this->item);
    }
}
