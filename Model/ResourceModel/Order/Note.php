<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Order;

class Note extends \M2E\Temu\Model\ResourceModel\ActiveRecord\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_ORDER_ID = 'order_id';
    public const COLUMN_NOTE = 'note';
    public const COLUMN_UPDATE_DATE = 'update_date';
    public const COLUMN_CREATE_DATE = 'create_date';

    public function _construct(): void
    {
        $this->_init(\M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_ORDER_NOTE, self::COLUMN_ID);
    }
}
