<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Order;

class Tax
{
    public float $taxTotal;
    public float $taxAfterDiscount;

    public function __construct(
        float $taxTotal,
        float $taxAfterDiscount
    ) {
        $this->taxTotal = $taxTotal;
        $this->taxAfterDiscount = $taxAfterDiscount;
    }
}
