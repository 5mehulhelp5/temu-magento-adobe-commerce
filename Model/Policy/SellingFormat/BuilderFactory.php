<?php

namespace M2E\Temu\Model\Policy\SellingFormat;

class BuilderFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Builder
    {
        return $this->objectManager->create(Builder::class);
    }
}
