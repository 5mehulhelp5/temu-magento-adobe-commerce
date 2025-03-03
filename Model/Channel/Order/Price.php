<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Order;

class Price
{
    public float $total;
    public float $delivery;
    public float $discount;

    public function __construct(
        float $total,
        float $delivery,
        float $discount
    ) {
        $this->total = $total;
        $this->delivery = $delivery;
        $this->discount = $discount;
    }
}
