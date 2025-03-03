<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\UnmanagedProduct\VariantSku;

/**
 * @method \M2E\Temu\Model\UnmanagedProduct\VariantSku getFirstItem()
 * @method \M2E\Temu\Model\UnmanagedProduct\VariantSku[] getItems()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();

        $this->_init(
            \M2E\Temu\Model\UnmanagedProduct\VariantSku::class,
            \M2E\Temu\Model\ResourceModel\UnmanagedProduct\VariantSku::class
        );
    }
}
