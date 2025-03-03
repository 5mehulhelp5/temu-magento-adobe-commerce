<?php

declare(strict_types=1);

namespace M2E\Temu\Model\InventorySync;

class ReceivedProductFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createEmpty(): ReceivedProduct
    {
        return $this->objectManager->create(ReceivedProduct::class);
    }

    public function create(
        string $channelProductId,
        int $accountId
    ): ReceivedProduct {
        $object = $this->createEmpty();
        $object->create($channelProductId, $accountId);

        return $object;
    }
}
