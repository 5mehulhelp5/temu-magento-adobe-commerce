<?php

declare(strict_types=1);

namespace M2E\Temu\Model\InventorySync;

use M2E\Temu\Model\ResourceModel\InventorySync\ReceivedProduct as ReceivedProductResource;

class ReceivedProduct extends \M2E\Temu\Model\ActiveRecord\AbstractModel
{
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(\M2E\Temu\Model\ResourceModel\InventorySync\ReceivedProduct::class);
    }

    public function create(
        string $channelProductId,
        int $accountId
    ): self {
        $this->setData(ReceivedProductResource::COLUMN_CHANNEL_PRODUCT_ID, $channelProductId)
             ->setData(ReceivedProductResource::COLUMN_ACCOUNT_ID, $accountId);

        return $this;
    }

    public function getAccountId(): int
    {
        return $this->getData(ReceivedProductResource::COLUMN_ACCOUNT_ID);
    }

    public function getChannelProductId(): string
    {
        return $this->getData(ReceivedProductResource::COLUMN_CHANNEL_PRODUCT_ID);
    }
}
