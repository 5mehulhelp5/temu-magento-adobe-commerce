<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Policy;

class Shipping extends \M2E\Temu\Model\ResourceModel\ActiveRecord\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_ACCOUNT_ID = 'account_id';
    public const COLUMN_TITLE = 'title';
    public const COLUMN_SHIPPING_TEMPLATE_ID = 'shipping_template_id';
    public const COLUMN_PREPARATION_TIME = 'preparation_time';
    public const COLUMN_UPDATE_DATE = 'update_date';
    public const COLUMN_CREATE_DATE = 'create_date';

    public function _construct(): void
    {
        $this->_init(
            \M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_TEMPLATE_SHIPPING,
            self::COLUMN_ID
        );
    }
}
