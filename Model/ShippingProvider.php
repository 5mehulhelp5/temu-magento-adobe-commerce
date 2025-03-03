<?php

declare(strict_types=1);

namespace M2E\Temu\Model;

use M2E\Temu\Model\ResourceModel\ShippingProvider as ShippingProviderResource;

class ShippingProvider extends \M2E\Temu\Model\ActiveRecord\AbstractModel
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(ShippingProviderResource::class);
    }

    public function create(
        Account $account,
        int $shippingProviderId,
        string $shippingProviderName,
        int $shippingProviderRegionId,
        string $shippingProviderRegionName
    ): self {
        $this->setData(ShippingProviderResource::COLUMN_ACCOUNT_ID, $account->getId())
             ->setData(ShippingProviderResource::COLUMN_SHIPPING_PROVIDER_ID, $shippingProviderId)
             ->setData(ShippingProviderResource::COLUMN_SHIPPING_PROVIDER_NAME, $shippingProviderName)
             ->setData(ShippingProviderResource::COLUMN_SHIPPING_PROVIDER_REGION_ID, $shippingProviderRegionId)
             ->setData(ShippingProviderResource::COLUMN_SHIPPING_PROVIDER_REGION_NAME, $shippingProviderRegionName);

        return $this;
    }

    public function getAccountId(): int
    {
        return (int)$this->getData(ShippingProviderResource::COLUMN_ACCOUNT_ID);
    }

    public function getShippingProviderId(): int
    {
        return (int)$this->getData(ShippingProviderResource::COLUMN_SHIPPING_PROVIDER_ID);
    }

    public function getShippingProviderName(): string
    {
        return (string)$this->getData(ShippingProviderResource::COLUMN_SHIPPING_PROVIDER_NAME);
    }

    public function setShippingProviderName(string $value): self
    {
        $this->setData(ShippingProviderResource::COLUMN_SHIPPING_PROVIDER_NAME, $value);

        return $this;
    }

    public function getShippingProviderRegionId(): int
    {
        return (int)$this->getData(ShippingProviderResource::COLUMN_SHIPPING_PROVIDER_REGION_ID);
    }

    public function setShippingProviderRegionId(int $value): self
    {
        $this->setData(ShippingProviderResource::COLUMN_SHIPPING_PROVIDER_REGION_ID, $value);

        return $this;
    }

    public function getShippingProviderRegionName(): string
    {
        return (string)$this->getData(ShippingProviderResource::COLUMN_SHIPPING_PROVIDER_REGION_NAME);
    }

    public function setShippingProviderRegionName(string $value): self
    {
        $this->setData(ShippingProviderResource::COLUMN_SHIPPING_PROVIDER_REGION_NAME, $value);

        return $this;
    }
}
