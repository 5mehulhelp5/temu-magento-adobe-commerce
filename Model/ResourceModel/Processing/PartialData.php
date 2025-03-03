<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Processing;

class PartialData extends \M2E\Temu\Model\ResourceModel\ActiveRecord\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_PROCESSING_ID = 'processing_id';
    public const COLUMN_PART_NUMBER = 'part_number';
    public const COLUMN_DATA = 'data';
    public const COLUMN_UPDATE_DATE = 'update_date';
    public const COLUMN_CREATE_DATE = 'create_date';

    public function _construct(): void
    {
        $this->_init(
            \M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_PROCESSING_PARTIAL_DATA,
            self::COLUMN_ID
        );
    }
}
