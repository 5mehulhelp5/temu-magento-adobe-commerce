<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Magento\Tax\Rule;

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
