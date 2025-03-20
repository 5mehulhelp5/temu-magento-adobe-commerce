<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Update\y25_m03;

use M2E\Temu\Helper\Module\Database\Tables;
use M2E\Temu\Model\ResourceModel\Product as ProductResource;

class AddColumnsToProductTable extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $modifier = $this->createTableModifier(Tables::TABLE_NAME_PRODUCT);

        $modifier->addColumn(
            ProductResource::COLUMN_ONLINE_DESCRIPTION,
            'VARCHAR(255)',
            null,
            ProductResource::COLUMN_ONLINE_TITLE,
            true,
            false
        );
        $modifier->addColumn(
            ProductResource::COLUMN_ONLINE_IMAGE,
            'VARCHAR(255)',
            null,
            ProductResource::COLUMN_ONLINE_DESCRIPTION,
            true,
            false
        );

        $modifier->addColumn(
            ProductResource::COLUMN_ONLINE_CATEGORIES_DATA,
            'LONGTEXT',
            null,
            ProductResource::COLUMN_ONLINE_CATEGORY_ID,
            false,
            false
        );

        $modifier->commit();
    }
}
