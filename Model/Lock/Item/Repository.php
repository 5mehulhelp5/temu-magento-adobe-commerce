<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Lock\Item;

class Repository
{
    private \M2E\Temu\Model\ResourceModel\Lock\Item $resource;
    private \M2E\Temu\Model\ResourceModel\Lock\Item\CollectionFactory $collectionFactory;
    private \M2E\Temu\Model\Lock\ItemFactory $itemFactory;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\Lock\Item $resource,
        \M2E\Temu\Model\ResourceModel\Lock\Item\CollectionFactory $collectionFactory,
        \M2E\Temu\Model\Lock\ItemFactory $itemFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->itemFactory = $itemFactory;
    }

    public function create(\M2E\Temu\Model\Lock\Item $item): void
    {
        $this->resource->save($item);
    }

    public function save(\M2E\Temu\Model\Lock\Item $item): void
    {
        $this->resource->save($item);
    }

    public function remove(\M2E\Temu\Model\Lock\Item $item): void
    {
        $this->resource->delete($item);
    }

    public function findById(int $id): ?\M2E\Temu\Model\Lock\Item
    {
        $object = $this->itemFactory->createEmpty();
        $this->resource->load($object, $id);

        if ($object->isObjectNew()) {
            return null;
        }

        return $object;
    }

    public function findByNick(string $nick): ?\M2E\Temu\Model\Lock\Item
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(\M2E\Temu\Model\ResourceModel\Lock\Item::COLUMN_NICK, $nick);

        $object = $collection->getFirstItem();
        if ($object->isObjectNew()) {
            return null;
        }

        return $object;
    }

    /**
     * @param int $parentId
     *
     * @return \M2E\Temu\Model\Lock\Item[]
     */
    public function findByParentId(int $parentId): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(\M2E\Temu\Model\ResourceModel\Lock\Item::COLUMN_PARENT_ID, $parentId);

        return array_values($collection->getItems());
    }
}
