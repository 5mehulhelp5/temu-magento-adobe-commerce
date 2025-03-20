<?php

namespace M2E\Temu\Model\ResourceModel\Category\Tree;

class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Category\Tree::class,
            \M2E\Temu\Model\ResourceModel\Category\Tree::class
        );
    }
}
