<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel;

class ShippingProvider extends \M2E\Temu\Model\ResourceModel\ActiveRecord\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_ACCOUNT_ID = 'account_id';
    public const COLUMN_SHIPPING_PROVIDER_ID = 'channel_shipping_provider_id';
    public const COLUMN_SHIPPING_PROVIDER_NAME = 'channel_shipping_provider_name';
    public const COLUMN_SHIPPING_PROVIDER_REGION_ID = 'channel_shipping_provider_region_id';
    public const COLUMN_SHIPPING_PROVIDER_REGION_NAME = 'channel_shipping_provider_region_name';

    public function _construct(): void
    {
        $this->_init(
            \M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_SHIPPING_PROVIDERS,
            self::COLUMN_ID
        );
    }
}
