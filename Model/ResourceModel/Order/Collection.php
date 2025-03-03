<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Order;

/**
 * @method \M2E\Temu\Model\Order[] getItems()
 * @method \M2E\Temu\Model\Order getFirstItem()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Order::class,
            \M2E\Temu\Model\ResourceModel\Order::class
        );
    }
}
