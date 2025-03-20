<?php

namespace M2E\Temu\Model\ResourceModel\Category\Dictionary;

/**
 * @method \M2E\Temu\Model\Category\Dictionary getFirstItem()
 * @method \M2E\Temu\Model\Category\Dictionary[] getItems()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Category\Dictionary::class,
            \M2E\Temu\Model\ResourceModel\Category\Dictionary::class
        );
    }
}
