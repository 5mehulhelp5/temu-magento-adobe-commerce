<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Policy\Description;

class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Policy\Description::class,
            \M2E\Temu\Model\ResourceModel\Policy\Description::class
        );
    }
}
