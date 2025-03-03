<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Order\Change;

/**
 * @method \M2E\Temu\Model\Order\Change[] getItems()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Order\Change::class,
            \M2E\Temu\Model\ResourceModel\Order\Change::class,
        );
    }
}
