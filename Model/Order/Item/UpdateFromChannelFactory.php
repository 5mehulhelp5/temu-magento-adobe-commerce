<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Item;

class UpdateFromChannelFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(
        \M2E\Temu\Model\Order $order,
        \M2E\Temu\Model\Channel\Order\Item $channelItem
    ): UpdateFromChannel {
        return $this->objectManager->create(
            UpdateFromChannel::class,
            [
                'order' => $order,
                'channelItem' => $channelItem,
            ]
        );
    }
}
