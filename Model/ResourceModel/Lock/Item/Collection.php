<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Lock\Item;

/**
 * @method \M2E\Temu\Model\Lock\Item getFirstItem()
 * @method \M2E\Temu\Model\Lock\Item[] getItems()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(
            \M2E\Temu\Model\Lock\Item::class,
            \M2E\Temu\Model\ResourceModel\Lock\Item::class
        );
    }
}
