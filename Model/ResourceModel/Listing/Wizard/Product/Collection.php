<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Listing\Wizard\Product;

/**
 * @method \M2E\Temu\Model\Listing\Wizard\Product[] getItems()
 * @method \M2E\Temu\Model\Listing\Wizard\Product getFirstItem()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Listing\Wizard\Product::class,
            \M2E\Temu\Model\ResourceModel\Listing\Wizard\Product::class
        );
    }
}
