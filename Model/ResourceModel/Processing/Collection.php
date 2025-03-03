<?php

namespace M2E\Temu\Model\ResourceModel\Processing;

class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct()
    {
        $this->_init(
            \M2E\Temu\Model\Processing::class,
            \M2E\Temu\Model\ResourceModel\Processing::class
        );
    }
}
