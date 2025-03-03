<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Lock\Transactional;

class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    //########################################

    public function _construct()
    {
        $this->_init(
            \M2E\Temu\Model\Lock\Transactional::class,
            \M2E\Temu\Model\ResourceModel\Lock\Transactional::class
        );
    }
}
