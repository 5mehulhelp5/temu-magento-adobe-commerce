<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Listing\Wizard;

class ProductFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Product
    {
        return $this->objectManager->create(Product::class);
    }
}
