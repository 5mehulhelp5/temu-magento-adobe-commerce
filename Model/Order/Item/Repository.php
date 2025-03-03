<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Item;

use M2E\Temu\Model\ResourceModel\Order\Item as OrderItemResource;

class Repository
{
    private \M2E\Temu\Model\ResourceModel\Order\Item\CollectionFactory $collectionFactory;
    private OrderItemResource $resource;
    private \M2E\Temu\Model\Order\ItemFactory $itemFactory;

    public function __construct(
        \M2E\Temu\Model\Order\ItemFactory $itemFactory,
        \M2E\Temu\Model\ResourceModel\Order\Item\CollectionFactory $collectionFactory,
        \M2E\Temu\Model\ResourceModel\Order\Item $resource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
        $this->itemFactory = $itemFactory;
    }

    public function find(int $id): ?\M2E\Temu\Model\Order\Item
    {
        $item = $this->itemFactory->createEmpty();
        $this->resource->load($item, $id);

        if ($item->isObjectNew()) {
            return null;
        }

        return $item;
    }

    /**
     * @return \M2E\Temu\Model\Order\Item[]
     */
    public function getByIds(array $ids): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(OrderItemResource::COLUMN_ID, ['in' => $ids]);

        return array_values($collection->getItems());
    }

    public function create(\M2E\Temu\Model\Order\Item $orderItem): void
    {
        $this->resource->save($orderItem);
    }

    public function save(\M2E\Temu\Model\Order\Item $orderItem): void
    {
        $this->resource->save($orderItem);
    }

    public function remove(\M2E\Temu\Model\Order\Item $orderItem): void
    {
        $this->resource->delete($orderItem);
    }

    public function findByOrder(\M2E\Temu\Model\Order $order): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(OrderItemResource::COLUMN_ORDER_ID, ['eq' => $order->getId()]);

        $result = [];
        foreach ($collection->getItems() as $item) {
            $item->initOrder($order);

            $result[] = $item;
        }

        return $result;
    }

    public function findByOrderIdAndOrderItemId(int $orderId, string $channelOrderItemId): ?\M2E\Temu\Model\Order\Item
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(OrderItemResource::COLUMN_ORDER_ID, $orderId);
        $collection->addFieldToFilter(OrderItemResource::COLUMN_CHANNEL_ORDER_ITEM_ID, $channelOrderItemId);

        $item = $collection->getFirstItem();

        if ($item->isObjectNew()) {
            return null;
        }

        return $item;
    }

    public function getOrderItemCollection(int $orderId): \M2E\Temu\Model\ResourceModel\Order\Item\Collection
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(OrderItemResource::COLUMN_ORDER_ID, ['eq' => $orderId]);

        return $collection;
    }

    public function getOrderItemCollectionByOrderIds(array $orderIds): \M2E\Temu\Model\ResourceModel\Order\Item\Collection
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(OrderItemResource::COLUMN_ORDER_ID, ['in' => $orderIds]);

        return $collection;
    }

    public function getOrderIdsBySearchValue(string $value): array
    {
        $collection = $this->collectionFactory->create();

        $collection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $collection->getSelect()->columns(OrderItemResource::COLUMN_ORDER_ID);
        $collection->getSelect()->distinct();

        $collection->addFieldToFilter(
            [
                OrderItemResource::COLUMN_PRODUCT_SKU,
                OrderItemResource::COLUMN_CHANNEL_PRODUCT_ID
            ],
            [
                ['like' => "%$value%"],
                ['like' => "%$value%"]
            ]
        );

        return $collection->getColumnValues(OrderItemResource::COLUMN_ORDER_ID);
    }
}
