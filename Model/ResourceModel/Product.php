<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel;

class Product extends \M2E\Temu\Model\ResourceModel\ActiveRecord\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_LISTING_ID = 'listing_id';
    public const COLUMN_CHANNEL_PRODUCT_ID = 'channel_product_id';
    public const COLUMN_MAGENTO_PRODUCT_ID = 'magento_product_id';
    public const COLUMN_IS_SIMPLE = 'is_simple';
    public const COLUMN_ONLINE_SKU = 'online_sku';
    public const COLUMN_STATUS = 'status';
    public const COLUMN_STATUS_CHANGER = 'status_changer';
    public const COLUMN_STATUS_CHANGE_DATE = 'status_change_date';
    public const COLUMN_ONLINE_TITLE = 'online_title';
    public const COLUMN_IDENTIFIERS = 'identifiers';
    public const COLUMN_ONLINE_QTY = 'online_qty';
    public const COLUMN_ONLINE_MIN_PRICE = 'online_min_price';
    public const COLUMN_ONLINE_MAX_PRICE = 'online_max_price';
    public const COLUMN_ONLINE_CATEGORY_ID  = 'online_category_id';
    public const COLUMN_LAST_BLOCKING_ERROR_DATE = 'last_blocking_error_date';
    public const COLUMN_ADDITIONAL_DATA = 'additional_data';
    public const COLUMN_UPDATE_DATE = 'update_date';
    public const COLUMN_CREATE_DATE = 'create_date';

    public function _construct(): void
    {
        $this->_init(
            \M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_PRODUCT,
            self::COLUMN_ID
        );
    }
}
