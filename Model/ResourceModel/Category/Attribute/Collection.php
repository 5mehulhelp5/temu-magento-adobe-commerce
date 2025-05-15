<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Category\Attribute;

/**
 * @method \M2E\Temu\Model\Category\CategoryAttribute[] getItems()
 * @method \M2E\Temu\Model\Category\CategoryAttribute getFirstItem()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Category\CategoryAttribute::class,
            \M2E\Temu\Model\ResourceModel\Category\Attribute::class
        );
    }
}
