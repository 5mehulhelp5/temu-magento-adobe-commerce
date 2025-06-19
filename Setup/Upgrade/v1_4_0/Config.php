<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Upgrade\v1_4_0;

class Config implements \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
{
    public function getFeaturesList(): array
    {
        return [
            \M2E\Temu\Setup\Update\y25_m06\AddConfigurableProducts::class,
            \M2E\Temu\Setup\Update\y25_m06\AddOnlineShippingColumnsToProductTable::class,
            \M2E\Temu\Setup\Update\y25_m06\AddReferenceLink::class,
            \M2E\Temu\Setup\Update\y25_m06\AddShippingReviseColumnToPolicy::class,
        ];
    }
}
