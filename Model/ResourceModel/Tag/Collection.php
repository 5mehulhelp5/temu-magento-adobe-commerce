<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel\Tag;

/**
 * @method \M2E\Temu\Model\Tag\Entity[] getItems()
 * @method \M2E\Temu\Model\Tag\Entity[] getFirstItem()
 */
class Collection extends \M2E\Temu\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \M2E\Temu\Model\Tag\Entity::class,
            \M2E\Temu\Model\ResourceModel\Tag::class
        );
    }
}
