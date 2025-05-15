<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Shipping;

class Value
{
    public string $shippingTemplateId;
    public int $preparationTime;

    public function __construct(
        string $shippingTemplateId,
        int $preparationTime
    ) {
        $this->shippingTemplateId = $shippingTemplateId;
        $this->preparationTime = $preparationTime;
    }
}
