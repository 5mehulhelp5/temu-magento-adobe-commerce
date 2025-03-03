<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Order\Item;

/**
 * @method \M2E\Temu\Model\Order\Item[] getItems()
 * @method \M2E\Temu\Model\Order\Item getFirstItem()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Order\Item::class,
            \M2E\Temu\Model\ResourceModel\Order\Item::class
        );
    }
}
