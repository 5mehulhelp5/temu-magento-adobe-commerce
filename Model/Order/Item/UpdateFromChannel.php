<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Item;

class UpdateFromChannel
{
    private \M2E\Temu\Model\Order $order;
    private \M2E\Temu\Model\Channel\Order\Item $channelItem;
    /** @var \M2E\Temu\Model\Order\Item\Repository */
    private Repository $orderItemRepository;
    /** @var \M2E\Temu\Model\Order\Item\UpdateFromChannel\CreateFactory */
    private UpdateFromChannel\CreateFactory $createFactory;
    /** @var \M2E\Temu\Model\Order\Item\UpdateFromChannel\UpdateFactory */
    private UpdateFromChannel\UpdateFactory $updateFactory;

    public function __construct(
        \M2E\Temu\Model\Order $order,
        \M2E\Temu\Model\Channel\Order\Item $channelItem,
        \M2E\Temu\Model\Order\Item\Repository $orderItemRepository,
        \M2E\Temu\Model\Order\Item\UpdateFromChannel\CreateFactory $createFactory,
        \M2E\Temu\Model\Order\Item\UpdateFromChannel\UpdateFactory $updateFactory
    ) {
        $this->order = $order;
        $this->channelItem = $channelItem;
        $this->orderItemRepository = $orderItemRepository;
        $this->createFactory = $createFactory;
        $this->updateFactory = $updateFactory;
    }

    public function process(): \M2E\Temu\Model\Order\Item
    {
        $item = $this->findExistItem();
        if ($item !== null) {
            $this->update($item);
        } else {
            $item = $this->create();
        }

        return $item;
    }

    private function findExistItem(): ?\M2E\Temu\Model\Order\Item
    {
        return $this->orderItemRepository->findByOrderIdAndOrderItemId(
            (int)$this->order->getId(),
            $this->channelItem->getId()
        );
    }

    private function update(\M2E\Temu\Model\Order\Item $item): void
    {
        $update = $this->updateFactory->create($item, $this->channelItem);

        $update->process();
    }

    private function create(): \M2E\Temu\Model\Order\Item
    {
        $create = $this->createFactory->create($this->order, $this->channelItem);

        return $create->process();
    }
}
