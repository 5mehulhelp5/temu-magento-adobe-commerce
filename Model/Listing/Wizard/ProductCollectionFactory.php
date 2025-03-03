<?php

namespace M2E\Temu\Model\Listing\Wizard;

class ProductCollectionFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): \M2E\Temu\Model\ResourceModel\Listing\Wizard\Product\Collection
    {
        return $this->objectManager->create(\M2E\Temu\Model\ResourceModel\Listing\Wizard\Product\Collection::class);
    }
}
