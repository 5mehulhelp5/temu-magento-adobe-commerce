<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\Deleted;

use M2E\Temu\Model\ResourceModel\Product\VariantSku\Deleted as VariantSkuDeletedResource;
use M2E\Temu\Model\ResourceModel\Product\VariantSku\Deleted\CollectionFactory as VariantSkuDeletedCollectionFactory;

class Repository
{
    /** @var \M2E\Temu\Model\ResourceModel\Product\VariantSku\Deleted */
    private VariantSkuDeletedResource $variantSkuDeletedResource;
    /** @var VariantSkuDeletedCollectionFactory */
    private VariantSkuDeletedResource\CollectionFactory $variantSkuDeletedCollectionFactory;

    public function __construct(
        VariantSkuDeletedResource $variantSkuDeletedResource,
        VariantSkuDeletedCollectionFactory $variantSkuDeletedCollectionFactory
    ) {
        $this->variantSkuDeletedResource = $variantSkuDeletedResource;
        $this->variantSkuDeletedCollectionFactory = $variantSkuDeletedCollectionFactory;
    }

    /**
     * @param int $productId
     *
     * @return array<\M2E\Temu\Model\Product\VariantSku\Deleted>
     */
    public function getByProductId(int $productId): array
    {
        $collection = $this->variantSkuDeletedCollectionFactory->create();
        $collection->addFieldToFilter(
            VariantSkuDeletedResource::COLUMN_PRODUCT_ID,
            ['eq' => $productId]
        );

        return array_values($collection->getItems());
    }

    public function hasByProductId(int $productId): bool
    {
        $collection = $this->variantSkuDeletedCollectionFactory->create();
        $collection->addFieldToFilter(
            VariantSkuDeletedResource::COLUMN_PRODUCT_ID,
            ['eq' => $productId]
        );

        return $collection->getSize() > 0;
    }

    public function create(\M2E\Temu\Model\Product\VariantSku\Deleted $deleted)
    {
        $this->variantSkuDeletedResource->save($deleted);
    }

    public function delete(\M2E\Temu\Model\Product\VariantSku\Deleted $deleted)
    {
        $this->variantSkuDeletedResource->delete($deleted);
    }
}
