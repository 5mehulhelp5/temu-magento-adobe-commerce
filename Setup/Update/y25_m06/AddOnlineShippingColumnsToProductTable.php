<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Update\y25_m06;

use M2E\Temu\Helper\Module\Database\Tables;
use M2E\Temu\Model\ResourceModel\Product as ProductResource;

class AddOnlineShippingColumnsToProductTable extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $modifier = $this->createTableModifier(Tables::TABLE_NAME_PRODUCT);

        $modifier->addColumn(
            ProductResource::COLUMN_ONLINE_SHIPPING_TEMPLATE_ID,
            'VARCHAR(255)',
            'NULL',
            ProductResource::COLUMN_ONLINE_CATEGORIES_DATA, //TODO check
            false,
            false
        );
        $modifier->addColumn(
            ProductResource::COLUMN_ONLINE_PREPARATION_TIME,
            'INT UNSIGNED',
            'NULL',
            ProductResource::COLUMN_ONLINE_SHIPPING_TEMPLATE_ID,
            false,
            false
        );

        $modifier->commit();
    }
}
