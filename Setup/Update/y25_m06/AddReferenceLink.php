<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Update\y25_m06;

use M2E\Temu\Helper\Module\Database\Tables;

class AddReferenceLink extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $modifier = $this->createTableModifier(Tables::TABLE_NAME_TEMPLATE_SELLING_FORMAT);

        $modifier->addColumn(
            \M2E\Temu\Model\ResourceModel\Policy\SellingFormat::COLUMN_REFERENCE_LINK_ATTRIBUTE,
            'VARCHAR(255)',
            'NULL',
            \M2E\Temu\Model\ResourceModel\Policy\SellingFormat::COLUMN_FIXED_PRICE_CUSTOM_ATTRIBUTE,
            false,
            false
        );

        $modifier->commit();

        $modifier = $this->createTableModifier(Tables::TABLE_NAME_PRODUCT_VARIANT_SKU);

        $modifier->addColumn(
            \M2E\Temu\Model\ResourceModel\Product\VariantSku::COLUMN_ONLINE_REFERENCE_LINK,
            'VARCHAR(255)',
            'NULL',
            null,
            false,
            false
        );

        $modifier->commit();
    }
}
