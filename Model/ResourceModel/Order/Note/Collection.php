<?php

namespace M2E\Temu\Model\ResourceModel\Order\Note;

class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public const ORDER_ID_FIELD = 'order_id';

    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Order\Note::class,
            \M2E\Temu\Model\ResourceModel\Order\Note::class
        );
    }

    /**
     * @return \M2E\Temu\Model\Order\Note[]
     */
    public function getItems()
    {
        /** @var \M2E\Temu\Model\Order\Note[] $items */
        $items = parent::getItems();

        return $items;
    }
}
