<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Account;

/**
 * @method \M2E\Temu\Model\Account[] getItems()
 * @method \M2E\Temu\Model\Account getFirstItem()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();

        $this->_init(
            \M2E\Temu\Model\Account::class,
            \M2E\Temu\Model\ResourceModel\Account::class
        );
    }
}
