<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Tax;

class ProductPriceTaxRate implements \M2E\Temu\Model\Order\Tax\PriceTaxRateInterface
{
    private float $taxAmount;
    private float $totalPrice;
    private bool $isEnabledRoundingOfValue;

    public function __construct(
        float $taxAmount,
        float $totalPrice,
        bool $isEnabledRoundingOfValue
    ) {
        $this->taxAmount = $taxAmount;
        $this->totalPrice = $totalPrice;
        $this->isEnabledRoundingOfValue = $isEnabledRoundingOfValue;
    }

    /**
     * @return float|int
     */
    public function getValue()
    {
        $rate = $this->getCalculatedValue();

        if ($rate === 0) {
            return $rate;
        }

        return $this->isEnabledRoundingOfValue
            ? $this->getRoundedRate($rate)
            : round($rate, 4);
    }

    /**
     * @return float|int
     */
    public function getNotRoundedValue()
    {
        $rate = $this->getCalculatedValue();

        return $rate === 0 ? $rate : round($rate, 4);
    }

    /**
     * @return float|int
     */
    private function getCalculatedValue()
    {
        if ($this->taxAmount <= 0) {
            return 0;
        }

        return ($this->taxAmount / $this->totalPrice) * 100;
    }

    public function isEnabledRoundingOfValue(): bool
    {
        return $this->isEnabledRoundingOfValue;
    }

    /**
     * @return int|float
     */
    private function getRoundedRate(float $rate)
    {
        $decimalPart = $rate - floor($rate);

        if ($decimalPart === 0.5) {
            $rate = round($rate, 2);
        } else {
            $rate = round($rate);
        }

        return $rate;
    }
}
