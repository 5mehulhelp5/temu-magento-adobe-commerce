<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku;

class DeletedFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Deleted
    {
        return $this->objectManager->create(Deleted::class);
    }
}
