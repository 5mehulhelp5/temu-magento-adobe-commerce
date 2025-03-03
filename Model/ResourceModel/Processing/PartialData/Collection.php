<?php

namespace M2E\Temu\Model\ResourceModel\Processing\PartialData;

class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Processing\PartialData::class,
            \M2E\Temu\Model\ResourceModel\Processing\PartialData::class
        );
    }
}
