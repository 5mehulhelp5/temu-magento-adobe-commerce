<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Tax;

use Magento\Framework\ObjectManagerInterface;

class PriceTaxRateFactory
{
    private ObjectManagerInterface $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createProductPriceTaxRateByOrder(\M2E\Temu\Model\Order $order): ProductPriceTaxRate
    {
        return $this->objectManager->create(
            ProductPriceTaxRate::class,
            [
                'taxAmount' => $order->getTaxAmount(),
                'totalPrice' => $order->getTotalPrice(),
                'isEnabledRoundingOfValue' => true,
            ]
        );
    }
}
