<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Order;

class Item extends \M2E\Temu\Model\ResourceModel\ActiveRecord\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_ORDER_ID = 'order_id';
    public const COLUMN_CHANNEL_ORDER_ITEM_ID = 'channel_order_item_id';
    public const COLUMN_ORDER_ITEM_STATUS = 'order_item_status';
    public const COLUMN_MAGENTO_PRODUCT_ID = 'magento_product_id';
    public const COLUMN_PRODUCT_DETAILS = 'product_details';
    public const COLUMN_QTY_RESERVED = 'qty_reserved';
    public const COLUMN_ADDITIONAL_DATA = 'additional_data';
    public const COLUMN_PRODUCT_TITLE = 'product_title';
    public const COLUMN_PRODUCT_SKU = 'product_sku';
    public const COLUMN_PRODUCT_SKU_ID = 'product_sku_id';
    public const COLUMN_CHANNEL_PRODUCT_ID = 'channel_product_id';
    public const COLUMN_QTY = 'qty';
    public const COLUMN_QTY_CANCELLED_BEFORE_SHIPMENT = 'qty_cancelled_before_shipment';
    public const COLUMN_SALE_PRICE = 'sale_price';
    public const COLUMN_BASE_PRICE = 'base_price';
    public const COLUMN_ORIGINAL_PRICE = 'original_price';
    public const COLUMN_FULFILLMENT_TYPE = 'fulfillment_type';
    public const COLUMN_TRACKING_DETAILS = 'tracking_details';
    public const COLUMN_UPDATE_DATE = 'update_date';
    public const COLUMN_CREATE_DATE = 'create_date';

    public function _construct(): void
    {
        $this->_init(
            \M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_ORDER_ITEM,
            self::COLUMN_ID
        );
    }
}
