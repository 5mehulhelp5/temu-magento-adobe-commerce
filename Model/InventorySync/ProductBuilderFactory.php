<?php

declare(strict_types=1);

namespace M2E\Temu\Model\InventorySync;

class ProductBuilderFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(\M2E\Temu\Model\Account $account): \M2E\Temu\Model\InventorySync\ProductBuilder
    {
        return $this->objectManager->create(
            \M2E\Temu\Model\InventorySync\ProductBuilder::class,
            [
                'account' => $account,
            ],
        );
    }
}
