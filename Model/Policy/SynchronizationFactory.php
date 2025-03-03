<?php

namespace M2E\Temu\Model\Policy;

class SynchronizationFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Synchronization
    {
        return $this->objectManager->create(Synchronization::class);
    }
}
