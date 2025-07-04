<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Log;

class Repository
{
    private \M2E\Temu\Model\ResourceModel\Order\Log $resource;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\Order\Log $resource
    ) {
        $this->resource = $resource;
    }

    public function save(\M2E\Temu\Model\Order\Log $orderLog)
    {
        $this->resource->save($orderLog);
    }

    public function removeByAccountId(int $accountId): void
    {
        $this->resource
            ->getConnection()
            ->delete(
                $this->resource->getMainTable(),
                ['account_id = ?' => $accountId],
            );
    }

    public function remove(?\DateTime $borderDate): void
    {
        $condition = [];
        if ($borderDate !== null) {
            $condition = [
                ' `create_date` < ? OR `create_date` IS NULL ' => $borderDate->format('Y-m-d H:i:s'),
            ];
        }

        $this->resource
            ->getConnection()
            ->delete($this->resource->getMainTable(), $condition);
    }
}
