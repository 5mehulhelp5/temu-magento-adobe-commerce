<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel;

class Order extends ActiveRecord\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_ACCOUNT_ID = 'account_id';
    public const COLUMN_SITE_ID = 'site_id';
    public const COLUMN_REGION_ID = 'region_id';
    public const COLUMN_STORE_ID = 'store_id';
    public const COLUMN_MAGENTO_ORDER_ID = 'magento_order_id';
    public const COLUMN_MAGENTO_ORDER_CREATION_FAILURE = 'magento_order_creation_failure';
    public const COLUMN_MAGENTO_ORDER_CREATION_FAILS_COUNT = 'magento_order_creation_fails_count';
    public const COLUMN_MAGENTO_ORDER_CREATION_LATEST_ATTEMPT_DATE = 'magento_order_creation_latest_attempt_date';
    public const COLUMN_RESERVATION_STATE = 'reservation_state';
    public const COLUMN_RESERVATION_START_DATE = 'reservation_start_date';
    public const COLUMN_CHANNEL_ORDER_ID = 'channel_order_id';
    public const COLUMN_ORDER_STATUS = 'order_status';
    public const COLUMN_PURCHASE_DATE = 'purchase_date';
    public const COLUMN_CHANNEL_UPDATE_DATE = 'channel_update_date';
    public const COLUMN_PRICE_TOTAL = 'price_total';
    public const COLUMN_PRICE_DELIVERY = 'price_delivery';
    public const COLUMN_PRICE_DISCOUNT = 'price_discount';
    public const COLUMN_CURRENCY = 'currency';
    public const COLUMN_TAX_DETAILS = 'tax_details';
    public const COLUMN_BUYER_NAME = 'buyer_name';
    public const COLUMN_BUYER_EMAIL = 'buyer_email';
    public const COLUMN_BUYER_PHONE = 'buyer_phone';
    public const COLUMN_SHIPPING_DETAILS = 'shipping_details';
    public const COLUMN_SHIP_BY_DATE = 'ship_by_date';
    public const COLUMN_SHIPPING_TIME = 'shipping_time';
    public const COLUMN_DELIVER_BY_DATE = 'deliver_by_date';
    public const COLUMN_ADDITIONAL_DATA = 'additional_data';
    public const COLUMN_UPDATE_DATE = 'update_date';
    public const COLUMN_CREATE_DATE = 'create_date';

    public function _construct(): void
    {
        $this->_init(
            \M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_ORDER,
            self::COLUMN_ID
        );
    }
}
