<?php

namespace M2E\Temu\Model\ResourceModel\OperationHistory;

/**
 * Class \M2E\Temu\Model\ResourceModel\OperationHistory\Collection
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    //########################################

    public function _construct()
    {
        $this->_init(
            \M2E\Temu\Model\OperationHistory::class,
            \M2E\Temu\Model\ResourceModel\OperationHistory::class
        );
    }

    //########################################
}
