<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product;

class QtyCalculatorFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(\M2E\Temu\Model\ProductInterface $product): QtyCalculator
    {
        return $this->objectManager->create(QtyCalculator::class, ['product' => $product]);
    }
}
