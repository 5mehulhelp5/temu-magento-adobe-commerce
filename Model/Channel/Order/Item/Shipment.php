<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Order\Item;

class Shipment
{
    public int $qty;
    public string $supplierName;
    public ?string $trackingNumber;

    public function __construct(
        int $qty,
        string $supplierName,
        ?string $trackingNumber
    ) {
        $this->qty = $qty;
        $this->supplierName = $supplierName;
        $this->trackingNumber = $trackingNumber;
    }
}
