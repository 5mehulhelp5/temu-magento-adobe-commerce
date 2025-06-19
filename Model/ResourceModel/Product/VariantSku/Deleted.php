<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Product\VariantSku;

class Deleted extends \M2E\Temu\Model\ResourceModel\ActiveRecord\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_PRODUCT_ID = 'product_id';
    public const COLUMN_REMOVED_MAGENTO_PRODUCT_ID = 'removed_magento_product_id';
    public const COLUMN_SKU_ID = 'sku_id';
    public const COLUMN_ONLINE_SKU = 'online_sku';
    public const COLUMN_ONLINE_PRICE = 'online_price';
    public const COLUMN_VARIATION_DATA = 'variation_data';
    public const COLUMN_CREATE_DATE = 'create_date';

    protected function _construct(): void
    {
        $this->_init(
            \M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_PRODUCT_VARIANT_SKU_DELETED,
            self::COLUMN_ID
        );
    }
}
