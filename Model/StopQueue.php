<?php

declare(strict_types=1);

namespace M2E\Temu\Model;

use M2E\Temu\Model\ResourceModel\StopQueue as ResourceModel;

class StopQueue extends \M2E\Temu\Model\ActiveRecord\AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();
        $this->_init(ResourceModel::class);
    }

    public function create(int $accountId, string $productId): self
    {
        return $this->setAccountId($accountId)
                    ->setChannelProductId($productId);
    }

    public function setAsProcessed(): void
    {
        $this->setData(ResourceModel::COLUMN_IS_PROCESSED, 1);
    }

    public function setAccountId(int $accountId): self
    {
        return $this->setData(ResourceModel::COLUMN_ACCOUNT_ID, $accountId);
    }

    public function setChannelProductId(string $channelProductId): self
    {
        return $this->setData(ResourceModel::COLUMN_CHANNEL_PRODUCT_ID, $channelProductId);
    }
}
