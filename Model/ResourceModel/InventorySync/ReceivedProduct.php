<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\InventorySync;

class ReceivedProduct extends \M2E\Temu\Model\ResourceModel\ActiveRecord\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_ACCOUNT_ID = 'account_id';
    public const COLUMN_CHANNEL_PRODUCT_ID = 'channel_product_id';
    public const COLUMN_CREATE_DATE = 'create_date';

    protected function _construct(): void
    {
        $this->_init(
            \M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_INVENTORY_SYNC_RECEIVED_PRODUCT,
            self::COLUMN_ID
        );
    }
}
