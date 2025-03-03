<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\UnmanagedProduct;

/**
 * @method \M2E\Temu\Model\UnmanagedProduct[] getItems()
 * @method \M2E\Temu\Model\UnmanagedProduct[] getFirstItem()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\UnmanagedProduct::class,
            \M2E\Temu\Model\ResourceModel\UnmanagedProduct::class
        );
    }
}
