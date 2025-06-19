<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Update\y25_m06;

use M2E\Temu\Helper\Module\Database\Tables as TablesHelper;
use M2E\Temu\Model\ResourceModel\Product\VariantSku\Deleted as ProductVariantSkuDeletedResource;
use Magento\Framework\DB\Ddl\Table;

class AddConfigurableProducts extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $this->addVariationAttributesColumnToProduct();
        $this->addVariationDataColumnToProductVariantSku();
        $this->createTableProductVariantSkuDeleted();
    }

    private function addVariationAttributesColumnToProduct(): void
    {
        $modifier = $this->createTableModifier(TablesHelper::TABLE_NAME_PRODUCT);

        $modifier->addColumn(
            \M2E\Temu\Model\ResourceModel\Product::COLUMN_VARIATION_ATTRIBUTES,
            'LONGTEXT',
            null,
            null,
            false,
            false
        );

        $modifier->commit();
    }

    private function addVariationDataColumnToProductVariantSku(): void
    {
        $modifier = $this->createTableModifier(TablesHelper::TABLE_NAME_PRODUCT_VARIANT_SKU);

        $modifier->addColumn(
            \M2E\Temu\Model\ResourceModel\Product\VariantSku::COLUMN_VARIATION_DATA,
            'LONGTEXT',
            null,
            null,
            false,
            false
        );

        $modifier->commit();
    }

    private function createTableProductVariantSkuDeleted()
    {
        $tableName = $this->getFullTableName(TablesHelper::TABLE_NAME_PRODUCT_VARIANT_SKU_DELETED);

        if ($this->getConnection()->isTableExists($tableName)) {
            return;
        }

        $listingProductVariantTable = $this->getConnection()->newTable($tableName);

        $listingProductVariantTable
            ->addColumn(
                ProductVariantSkuDeletedResource::COLUMN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'primary' => true,
                    'nullable' => false,
                    'auto_increment' => true,
                ]
            )
            ->addColumn(
                ProductVariantSkuDeletedResource::COLUMN_PRODUCT_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                ProductVariantSkuDeletedResource::COLUMN_REMOVED_MAGENTO_PRODUCT_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                ProductVariantSkuDeletedResource::COLUMN_SKU_ID,
                Table::TYPE_TEXT,
                50
            )
            ->addColumn(
                ProductVariantSkuDeletedResource::COLUMN_ONLINE_SKU,
                Table::TYPE_TEXT,
                255,
                ['default' => null]
            )
            ->addColumn(
                ProductVariantSkuDeletedResource::COLUMN_ONLINE_PRICE,
                Table::TYPE_DECIMAL,
                [12, 4],
                ['unsigned' => true, 'default' => null]
            )
            ->addColumn(
                ProductVariantSkuDeletedResource::COLUMN_VARIATION_DATA,
                Table::TYPE_TEXT,
                \M2E\Core\Model\ResourceModel\Setup::LONG_COLUMN_SIZE,
                ['nullable' => true, 'default' => null]
            )
            ->addColumn(
                ProductVariantSkuDeletedResource::COLUMN_CREATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addIndex('product_id', ProductVariantSkuDeletedResource::COLUMN_PRODUCT_ID)
            ->addIndex('magento_product_id', ProductVariantSkuDeletedResource::COLUMN_REMOVED_MAGENTO_PRODUCT_ID)
            ->addIndex('sku_id', ProductVariantSkuDeletedResource::COLUMN_SKU_ID)
            ->setOption('type', 'INNODB')
            ->setOption('charset', 'utf8')
            ->setOption('collate', 'utf8_general_ci')
            ->setOption('row_format', 'dynamic');

        $this->getConnection()->createTable($listingProductVariantTable);
    }
}
