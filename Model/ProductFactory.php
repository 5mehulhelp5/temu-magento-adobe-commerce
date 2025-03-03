<?php

declare(strict_types=1);

namespace M2E\Temu\Model;

class ProductFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createEmpty(): Product
    {
        return $this->objectManager->create(Product::class);
    }

    public function create(
        \M2E\Temu\Model\Listing $listing,
        int $magentoProductId,
        bool $isSimple
    ): Product {
        $obj = $this->createEmpty();
        $obj->create(
            $listing,
            $magentoProductId,
            $isSimple
        );

        return $obj;
    }
}
