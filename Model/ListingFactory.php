<?php

declare(strict_types=1);

namespace M2E\Temu\Model;

class ListingFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createEmpty(): Listing
    {
        return $this->objectManager->create(Listing::class);
    }
}
