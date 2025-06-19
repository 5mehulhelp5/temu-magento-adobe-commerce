<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Update;

class Config implements \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
{
    public function getFeaturesList(): array
    {
        return [
            'y25_m03' => [
                \M2E\Temu\Setup\Update\y25_m03\RemoveOldCronValues::class,
                \M2E\Temu\Setup\Update\y25_m03\AddRegionToAccount::class,
                \M2E\Temu\Setup\Update\y25_m03\AddDescriptionTemplateTable::class,
                \M2E\Temu\Setup\Update\y25_m03\AddShippingPolicy::class,
                \M2E\Temu\Setup\Update\y25_m03\AddCategoryTables::class,
                \M2E\Temu\Setup\Update\y25_m03\AddTemplateCategoryToProduct::class,
                \M2E\Temu\Setup\Update\y25_m03\AddColumnsToProductTable::class,
                \M2E\Temu\Setup\Update\y25_m03\AddOnlineImageVariantSku::class,
                \M2E\Temu\Setup\Update\y25_m03\AddAttributeMappingTable::class
            ],
            'y25_m04' => [
                \M2E\Temu\Setup\Update\y25_m04\RemoveAttributeMappingTable::class,
            ],
            'y25_m06' => [
                \M2E\Temu\Setup\Update\y25_m06\AddConfigurableProducts::class,
                \M2E\Temu\Setup\Update\y25_m06\AddShippingReviseColumnToPolicy::class,
                \M2E\Temu\Setup\Update\y25_m06\AddOnlineShippingColumnsToProductTable::class,
                \M2E\Temu\Setup\Update\y25_m06\AddReferenceLink::class,
            ],
        ];
    }
}
