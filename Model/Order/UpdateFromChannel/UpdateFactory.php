<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\UpdateFromChannel;

class UpdateFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(
        \M2E\Temu\Model\Order $order,
        \M2E\Temu\Model\Channel\Order $channelOrder
    ): Update {
        return $this->objectManager->create(
            Update::class,
            [
                'order' => $order,
                'channelOrder' => $channelOrder,
            ]
        );
    }
}
