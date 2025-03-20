<?php

namespace M2E\Temu\Model\ResourceModel\Category\Attribute;

class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Category\CategoryAttribute::class,
            \M2E\Temu\Model\ResourceModel\Category\Attribute::class
        );
    }
}
