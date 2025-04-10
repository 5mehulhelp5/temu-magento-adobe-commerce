<?php

declare(strict_types=1);

namespace M2E\Temu\Model\StopQueue;

use M2E\Temu\Model\ResourceModel\StopQueue as ResourceModel;

class Repository
{
    private ResourceModel\CollectionFactory $collectionFactory;
    /** @var \M2E\Temu\Model\ResourceModel\StopQueue */
    private ResourceModel $stopQueueResource;
    private \M2E\Temu\Model\ResourceModel\Account $accountResource;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\StopQueue $stopQueueResource,
        \M2E\Temu\Model\ResourceModel\Account $accountResource,
        ResourceModel\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->stopQueueResource = $stopQueueResource;
        $this->accountResource = $accountResource;
    }

    public function create(\M2E\Temu\Model\StopQueue $stopQueue): void
    {
        $this->stopQueueResource->save($stopQueue);
    }

    public function save(\M2E\Temu\Model\StopQueue $stopQueue): void
    {
        $this->stopQueueResource->save($stopQueue);
    }

    /**
     * @param int $limit
     *
     * @return \M2E\Temu\Model\StopQueue[]
     */
    public function findNotProcessed(int $limit): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(ResourceModel::COLUMN_IS_PROCESSED, 0);
        $collection->setOrder(ResourceModel::COLUMN_CREATE_DATE, \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        $collection->getSelect()->limit($limit);

        return array_values($collection->getItems());
    }

    public function deleteCompletedAfterBorderDate(\DateTime $borderDate): void
    {
        $collection = $this->collectionFactory->create();
        $collection->getConnection()->delete(
            $collection->getMainTable(),
            [
                ResourceModel::COLUMN_IS_PROCESSED . ' = ?' => 1,
                ResourceModel::COLUMN_UPDATE_DATE . ' < ?' => $borderDate->format('Y-m-d H:i:s'),
            ]
        );
    }

    public function getAccounts(): array
    {
        $collection = $this->collectionFactory->create();
        $connection = $collection->getConnection();
        $select = $connection->select();
        $select->from($collection->getMainTable())
               ->reset(\Magento\Framework\DB\Select::COLUMNS)
               ->columns(
                   [
                       'account_id',
                   ]
               )
               ->group('account_id')
               ->joinInner(
                   [
                       'account' => $this->accountResource->getMainTable()
                   ],
                   $collection->getMainTable() . ".account_id
                   = account.id",
                   ['server_hash']
               )
                ->where(ResourceModel::COLUMN_IS_PROCESSED . '=?', 0);

        return $connection->fetchAll($select);
    }

    public function getChannelProductIdsByAccount(int $accountId, int $limit): array
    {
        $collection = $this->collectionFactory->create();
        $connection = $collection->getConnection();
        $select = $connection->select();
        $select->from($collection->getMainTable())
               ->reset(\Magento\Framework\DB\Select::COLUMNS)
               ->columns(
                   [
                       'channel_product_id',
                   ]
               )
               ->limit($limit)
               ->where(ResourceModel::COLUMN_IS_PROCESSED . '=?', 0)
               ->where(ResourceModel::COLUMN_ACCOUNT_ID . '=?', $accountId);

        return $connection->fetchCol($select);
    }

    public function massStatusUpdate(array $channelIds, int $accountId): void
    {
        $collection = $this->collectionFactory->create();
        $collection->getConnection()->update(
            $collection->getMainTable(),
            [
                ResourceModel::COLUMN_IS_PROCESSED => 1,
            ],
            [
                ResourceModel::COLUMN_CHANNEL_PRODUCT_ID . ' IN (?)' => $channelIds,
                ResourceModel::COLUMN_ACCOUNT_ID . ' = ?' => $accountId,
            ]
        );
    }

    public function removeByAccountId(int $accountId): void
    {
        $this->stopQueueResource
            ->getConnection()
            ->delete(
                $this->stopQueueResource->getMainTable(),
                ['account_id = ?' => $accountId],
            );
    }
}
