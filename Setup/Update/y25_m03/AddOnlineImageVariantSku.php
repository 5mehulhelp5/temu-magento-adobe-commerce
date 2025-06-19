<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Update\y25_m03;

use M2E\Temu\Helper\Module\Database\Tables;
use M2E\Temu\Model\ResourceModel\Product\VariantSku as VariantSkuResource;

class AddOnlineImageVariantSku extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $modifier = $this->createTableModifier(Tables::TABLE_NAME_PRODUCT_VARIANT_SKU);

        $modifier->addColumn(
            VariantSkuResource::COLUMN_ONLINE_IMAGE,
            'VARCHAR(255)',
            null,
            VariantSkuResource::COLUMN_ONLINE_QTY,
            false,
            false
        );

        $modifier->commit();
    }
}
