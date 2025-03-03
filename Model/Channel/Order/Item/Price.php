<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Order\Item;

class Price
{
    public float $unitRetail;
    public float $unitBase;

    public function __construct(
        float $unitRetail,
        float $unitBase
    ) {
        $this->unitRetail = $unitRetail;
        $this->unitBase = $unitBase;
    }
}
