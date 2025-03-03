<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Item\UpdateFromChannel;

class CreateFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(
        \M2E\Temu\Model\Order $order,
        \M2E\Temu\Model\Channel\Order\Item $channelItem
    ): Create {
        return $this->objectManager->create(
            Create::class,
            [
                'order' => $order,
                'channelItem' => $channelItem,
            ]
        );
    }
}
