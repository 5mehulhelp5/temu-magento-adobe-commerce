<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product;

class SnapshotBuilderFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): SnapshotBuilder
    {
        return $this->objectManager->create(SnapshotBuilder::class);
    }
}
