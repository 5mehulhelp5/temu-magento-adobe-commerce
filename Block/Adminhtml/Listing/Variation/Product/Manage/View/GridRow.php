<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Listing\Variation\Product\Manage\View;

class GridRow extends \Magento\Framework\DataObject
{
    public const COLUMN_IS_DELETED = 'is_deleted';
    public const COLUMN_PRODUCT_ID = 'product_id';
    public const COLUMN_MAGENTO_PRODUCT_ID = 'magento_product_id';
    public const COLUMN_SKU = 'sku';
    public const COLUMN_SKU_ID = 'sku_id';
    public const COLUMN_STATUS = 'status';
    public const COLUMN_ONLINE_SKU = 'online_sku';
    public const COLUMN_ONLINE_PRICE = 'online_price';
    public const COLUMN_ONLINE_QTY = 'online_qty';
    public const COLUMN_VARIATION_DATA = 'variation_data';

    private ?\Magento\Catalog\Model\Product $magentoProduct = null;

    public function isVariationDeleted(): bool
    {
        return (bool)$this->getData(self::COLUMN_IS_DELETED);
    }

    public function getMagentoProductId(): ?int
    {
        $columnData = $this->getData(self::COLUMN_MAGENTO_PRODUCT_ID);
        if (empty($columnData)) {
            return null;
        }

        return (int)$columnData;
    }

    public function getVariationData(): \M2E\Temu\Model\Product\VariantSku\Dto\VariationData
    {
        return (new \M2E\Temu\Model\Product\VariantSku\Dto\VariationData())
            ->importFromJson((string)$this->getData(self::COLUMN_VARIATION_DATA));
    }

    public function isStatusNodListed(): bool
    {
        return $this->getStatus() === \M2E\Temu\Model\Product::STATUS_NOT_LISTED;
    }

    public function getStatus(): int
    {
        return (int)$this->getData(self::COLUMN_STATUS);
    }

    public function hasMagentoProductId(): bool
    {
        return $this->magentoProduct !== null;
    }

    public function setMagentoProduct(\Magento\Catalog\Model\Product $magentoProduct)
    {
        $this->magentoProduct = $magentoProduct;
    }

    public function getMagentoProduct(): ?\Magento\Catalog\Model\Product
    {
        return $this->magentoProduct;
    }

    public function getOnlineSku()
    {
        return $this->getData(self::COLUMN_ONLINE_SKU);
    }
}
