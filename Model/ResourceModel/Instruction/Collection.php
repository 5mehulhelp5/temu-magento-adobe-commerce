<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Instruction;

/**
 * @method \M2E\Temu\Model\Instruction[] getItems()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct(): void
    {
        $this->_init(
            \M2E\Temu\Model\Instruction::class,
            \M2E\Temu\Model\ResourceModel\Instruction::class
        );
    }
}
