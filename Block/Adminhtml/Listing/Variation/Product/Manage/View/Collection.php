<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Listing\Variation\Product\Manage\View;

use M2E\Temu\Model\ResourceModel\Product\VariantSku\Deleted as VariantSkuDeletedResource;

class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    private \M2E\Temu\Model\ResourceModel\Product\VariantSku $variantSkuResource;
    private VariantSkuDeletedResource $variantSkuDeletedResource;
    private \M2E\Core\Helper\Module\Database\Structure $dbStructureHelper;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \M2E\Temu\Model\ResourceModel\Product\VariantSku $variantSkuResource,
        VariantSkuDeletedResource $variantSkuDeletedResource,
        \M2E\Core\Helper\Module\Database\Structure $dbStructureHelper,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->variantSkuResource = $variantSkuResource;
        $this->variantSkuDeletedResource = $variantSkuDeletedResource;
        $this->dbStructureHelper = $dbStructureHelper;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    public function _construct(): void
    {
        parent::_construct();
        $this->_init(
            GridRow::class,
            \Magento\Framework\App\ResourceConnection::class
        );
    }

    protected function _initSelect()
    {
        $variantSkuSelect = $this->resourceConnection->getConnection()->select();
        $variantSkuSelect->from(
            ['variant_sku' => $this->variantSkuResource->getMainTable()],
            [
                GridRow::COLUMN_IS_DELETED => new \Zend_Db_Expr('0'),
                GridRow::COLUMN_PRODUCT_ID => 'product_id',
                GridRow::COLUMN_MAGENTO_PRODUCT_ID => 'magento_product_id',
                GridRow::COLUMN_SKU_ID => 'sku_id',
                GridRow::COLUMN_STATUS => 'status',
                GridRow::COLUMN_ONLINE_SKU => 'online_sku',
                GridRow::COLUMN_ONLINE_PRICE => 'online_price',
                GridRow::COLUMN_ONLINE_QTY => 'online_qty',
                GridRow::COLUMN_VARIATION_DATA => 'variation_data',
            ]
        );

        $variantSkuDeletedSelect = $this->resourceConnection->getConnection()->select();
        $variantSkuDeletedSelect->from(
            ['variant_sku_deleted' => $this->variantSkuDeletedResource->getMainTable()],
            [
                GridRow::COLUMN_IS_DELETED => new \Zend_Db_Expr('1'),
                GridRow::COLUMN_PRODUCT_ID => VariantSkuDeletedResource::COLUMN_PRODUCT_ID,
                GridRow::COLUMN_MAGENTO_PRODUCT_ID => VariantSkuDeletedResource::COLUMN_REMOVED_MAGENTO_PRODUCT_ID,
                GridRow::COLUMN_SKU_ID => VariantSkuDeletedResource::COLUMN_SKU_ID,
                GridRow::COLUMN_STATUS => new \Zend_Db_Expr(\M2E\Temu\Model\Product::STATUS_INACTIVE),
                GridRow::COLUMN_ONLINE_SKU => VariantSkuDeletedResource::COLUMN_ONLINE_SKU,
                GridRow::COLUMN_ONLINE_PRICE => new \Zend_Db_Expr(0),
                GridRow::COLUMN_ONLINE_QTY => new \Zend_Db_Expr(0),
                GridRow::COLUMN_VARIATION_DATA => VariantSkuDeletedResource::COLUMN_VARIATION_DATA,
            ]
        );

        $unionSelect = $this->resourceConnection
            ->getConnection()
            ->select()
            ->union([$variantSkuSelect, $variantSkuDeletedSelect]);

        $this->getSelect()->from(['main_table' => $unionSelect]);

        $entityTableName = $this->dbStructureHelper->getTableNameWithPrefix('catalog_product_entity');
        $this->getSelect()->joinLeft(
            ['magento_product' => $entityTableName],
            sprintf('main_table.%s = magento_product.entity_id', GridRow::COLUMN_MAGENTO_PRODUCT_ID),
            [
                GridRow::COLUMN_SKU => 'sku'
            ]
        );

        return $this;
    }
}
