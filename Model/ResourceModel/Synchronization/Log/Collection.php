<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Synchronization\Log;

class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Synchronization\Log::class,
            \M2E\Temu\Model\ResourceModel\Synchronization\Log::class
        );
    }
}
