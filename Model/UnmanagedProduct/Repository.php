<?php

declare(strict_types=1);

namespace M2E\Temu\Model\UnmanagedProduct;

use M2E\Temu\Model\ResourceModel\UnmanagedProduct as UnmanagedProductResource;
use M2E\Temu\Model\ResourceModel\InventorySync\ReceivedProduct as InventorySyncReceivedProductResource;
use Magento\Ui\Component\MassAction\Filter as MassActionFilter;
use M2E\Temu\Model\ResourceModel\UnmanagedProduct\VariantSku as VariantSkuResource;

class Repository
{
    private \M2E\Temu\Model\ResourceModel\UnmanagedProduct\CollectionFactory $collectionUnmanagedFactory;
    private \M2E\Temu\Model\ResourceModel\UnmanagedProduct $unmanagedResource;
    private \M2E\Temu\Model\UnmanagedProductFactory $objectFactory;
    private \M2E\Temu\Helper\Module\Database\Structure $dbStructureHelper;
    /** @var \M2E\Temu\Model\ResourceModel\InventorySync\ReceivedProduct */
    private InventorySyncReceivedProductResource $inventorySyncReceivedProductResource;
    private \M2E\Temu\Model\ResourceModel\UnmanagedProduct\VariantSku\CollectionFactory $productVariantCollectionFactory;
    private VariantSkuResource $variantResource;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\UnmanagedProduct\CollectionFactory $collectionFactory,
        \M2E\Temu\Model\ResourceModel\UnmanagedProduct $unmanagedResource,
        \M2E\Temu\Model\UnmanagedProductFactory $unmanagedProductFactory,
        \M2E\Temu\Model\ResourceModel\InventorySync\ReceivedProduct $inventorySyncReceivedProductResource,
        \M2E\Temu\Model\ResourceModel\UnmanagedProduct\VariantSku\CollectionFactory $productVariantCollectionFactory,
        \M2E\Temu\Model\ResourceModel\UnmanagedProduct\VariantSku $variantResource,
        \M2E\Temu\Helper\Module\Database\Structure $dbStructureHelper
    ) {
        $this->collectionUnmanagedFactory = $collectionFactory;
        $this->unmanagedResource = $unmanagedResource;
        $this->objectFactory = $unmanagedProductFactory;
        $this->dbStructureHelper = $dbStructureHelper;
        $this->inventorySyncReceivedProductResource = $inventorySyncReceivedProductResource;
        $this->variantResource = $variantResource;
        $this->productVariantCollectionFactory = $productVariantCollectionFactory;
    }

    public function createCollection(): \M2E\Temu\Model\ResourceModel\UnmanagedProduct\Collection
    {
        return $this->collectionUnmanagedFactory->create();
    }

    public function create(\M2E\Temu\Model\UnmanagedProduct $unmanaged): void
    {
        $this->unmanagedResource->save($unmanaged);
    }

    public function save(\M2E\Temu\Model\UnmanagedProduct $unmanaged): void
    {
        $this->unmanagedResource->save($unmanaged);
    }

    public function saveVariants(array $variantsSku): void
    {
        foreach ($variantsSku as $variantSku) {
            $this->saveVariant($variantSku);
        }
    }

    public function saveVariant(\M2E\Temu\Model\UnmanagedProduct\VariantSku $variant): void
    {
        $this->variantResource->save($variant);
    }

    /**
     * @throws \M2E\Temu\Model\Exception
     */
    public function get(int $id): \M2E\Temu\Model\UnmanagedProduct
    {
        $obj = $this->objectFactory->createEmpty();
        $this->unmanagedResource->load($obj, $id);

        if ($obj->isObjectNew()) {
            throw new \M2E\Temu\Model\Exception("Object by id $id not found.");
        }

        return $obj;
    }

    public function delete(\M2E\Temu\Model\UnmanagedProduct $listingProduct): void
    {
        $this->unmanagedResource->delete($listingProduct);
    }

    public function deleteVariant(\M2E\Temu\Model\UnmanagedProduct\VariantSku $variantSku): void
    {
        $this->variantResource->delete($variantSku);
    }

    /**
     * @return \M2E\Temu\Model\UnmanagedProduct[]
     */
    public function findByIds(array $ids): array
    {
        $collection = $this->collectionUnmanagedFactory->create();
        $collection->addFieldToFilter(
            UnmanagedProductResource::COLUMN_ID,
            ['in' => $ids],
        );

        return array_values($collection->getItems());
    }

    /**
     * @param int $id
     *
     * @return \M2E\Temu\Model\UnmanagedProduct|null
     */
    public function findById(int $id): ?\M2E\Temu\Model\UnmanagedProduct
    {
        $obj = $this->objectFactory->createEmpty();
        $this->unmanagedResource->load($obj, $id);

        if ($obj->isObjectNew()) {
            return null;
        }

        return $obj;
    }

    /**
     * @param int[] $ids
     * @param int $accountId
     *
     * @return \M2E\Temu\Model\UnmanagedProduct[]
     */
    public function findByChannelIds(array $ids, int $accountId): array
    {
        $collection = $this->collectionUnmanagedFactory->create();
        $collection
            ->addFieldToFilter(
                UnmanagedProductResource::COLUMN_CHANNEL_PRODUCT_ID,
                ['in' => $ids],
            )
            ->addFieldToFilter(UnmanagedProductResource::COLUMN_ACCOUNT_ID, $accountId);

        return array_values($collection->getItems());
    }

    /**
     * @param int $accountId
     *
     * @return void
     */
    public function removeProductByAccount(int $accountId): void
    {
        $collection = $this->collectionUnmanagedFactory->create();
        $collection->getConnection()->delete(
            $collection->getMainTable(),
            ['account_id = ?' => $accountId],
        );
    }

    public function removeVariantsByAccountId(int $accountId): void
    {
        $collection = $this->productVariantCollectionFactory->create();
        $collection->getConnection()->delete(
            $collection->getMainTable(),
            ['account_id = ?' => $accountId],
        );
    }

    /**
     * @param \M2E\Temu\Model\UnmanagedProduct $product
     *
     * @return \M2E\Temu\Model\UnmanagedProduct\VariantSku[]
     */
    public function findVariantsByProduct(\M2E\Temu\Model\UnmanagedProduct $product): array
    {
        $collection = $this->productVariantCollectionFactory->create();
        $collection->addFieldToFilter(VariantSkuResource::COLUMN_PRODUCT_ID, $product->getId());

        return array_values($collection->getItems());
    }

    /**
     * @param int $id
     *
     * @return \M2E\Temu\Model\UnmanagedProduct\VariantSku[]
     */
    public function findVariantsByMagentoProductId(int $id): array
    {
        $collection = $this->productVariantCollectionFactory->create();
        $collection->addFieldToFilter(VariantSkuResource::COLUMN_MAGENTO_PRODUCT_ID, $id);

        return array_values($collection->getItems());
    }

    public function findVariantBySkuIdAndAccountId(string $skuId, int $accountId): ?\M2E\Temu\Model\UnmanagedProduct\VariantSku
    {
        $collection = $this->productVariantCollectionFactory->create();
        $collection->addFieldToFilter(VariantSkuResource::COLUMN_SKU_ID, $skuId);
        $collection->addFieldToFilter(VariantSkuResource::COLUMN_ACCOUNT_ID, $accountId);

        /**
         *
         * @var \M2E\Temu\Model\UnmanagedProduct\VariantSku $item
         */
        $item = $collection->getFirstItem();
        if ($item->isObjectNew()) {
            return null;
        }

        return $item;
    }

    /**
     * @param int $magentoProductId
     *
     * @return \M2E\Temu\Model\UnmanagedProduct[]
     */
    public function findProductByMagentoProduct(int $magentoProductId): array
    {
        $collection = $this->collectionUnmanagedFactory->create();
        $collection->addFieldToFilter(UnmanagedProductResource::COLUMN_MAGENTO_PRODUCT_ID, $magentoProductId);

        return array_values($collection->getItems());
    }

    public function findRemovedMagentoProductIds(): array
    {
        $collection = $this->createCollection();

        $collection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $collection->getSelect()->columns(
            UnmanagedProductResource::COLUMN_MAGENTO_PRODUCT_ID
        );
        $collection->addFieldToFilter(UnmanagedProductResource::COLUMN_MAGENTO_PRODUCT_ID, ['notnull' => true]);
        $collection->getSelect()->distinct();

        $entityTableName = $this->dbStructureHelper->getTableNameWithPrefix('catalog_product_entity');

        $collection->getSelect()->joinLeft(
            ['cpe' => $entityTableName],
            sprintf(
                'cpe.entity_id = `main_table`.%s',
                UnmanagedProductResource::COLUMN_MAGENTO_PRODUCT_ID
            ),
            []
        );
        $collection->getSelect()->where('cpe.entity_id IS NULL');

        $result = [];
        foreach ($collection->toArray()['items'] ?? [] as $row) {
            $result[] = (int)$row[UnmanagedProductResource::COLUMN_MAGENTO_PRODUCT_ID];
        }

        return $result;
    }

    public function findRemovedFromChannel(int $accountId): array
    {
        $collection = $this->collectionUnmanagedFactory->create();

        $collection->joinLeft(
            [
                'isrp' => $this->inventorySyncReceivedProductResource->getMainTable(),
            ],
            implode(' AND ', [
                sprintf(
                    '`isrp`.%s = `main_table`.%s',
                    InventorySyncReceivedProductResource::COLUMN_CHANNEL_PRODUCT_ID,
                    UnmanagedProductResource::COLUMN_CHANNEL_PRODUCT_ID,
                ),
                sprintf(
                    '`isrp`.%s = `main_table`.%s',
                    InventorySyncReceivedProductResource::COLUMN_ACCOUNT_ID,
                    UnmanagedProductResource::COLUMN_ACCOUNT_ID,
                )
            ]),
            [],
        );

        $collection
            ->addFieldToFilter(
                sprintf(
                    'main_table.%s',
                    UnmanagedProductResource::COLUMN_ACCOUNT_ID
                ),
                $accountId
            )
            ->addFieldToFilter('isrp.id', ['null' => true]);

        return array_values($collection->getItems());
    }

    // ----------------------------------------

    public function isExistForAccount(int $accountId): bool
    {
        $collection = $this->collectionUnmanagedFactory->create();
        $collection->addFieldToFilter(UnmanagedProductResource::COLUMN_ACCOUNT_ID, $accountId);

        return (int)$collection->getSize() > 0;
    }

    /**
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param int $accountId
     *
     * @return \M2E\Temu\Model\UnmanagedProduct[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function findForUnmappingByMassActionSelectedProducts(MassActionFilter $filter, int $accountId): array
    {
        $collection = $this->collectionUnmanagedFactory->create();
        $filter->getCollection($collection);

        $collection->addFieldToFilter(
            UnmanagedProductResource::COLUMN_MAGENTO_PRODUCT_ID,
            ['notnull' => true]
        );

        $collection->addFieldToFilter(
            UnmanagedProductResource::COLUMN_ACCOUNT_ID,
            $accountId
        );

        return array_values($collection->getItems());
    }

    /**
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param int $accountId
     *
     * @return \M2E\Temu\Model\UnmanagedProduct[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function findForMovingByMassActionSelectedProducts(MassActionFilter $filter, int $accountId): array
    {
        $collection = $this->collectionUnmanagedFactory->create();
        $filter->getCollection($collection);

        $collection->addFieldToFilter(
            UnmanagedProductResource::COLUMN_MAGENTO_PRODUCT_ID,
            ['notnull' => true]
        );

        $collection->addFieldToFilter(
            UnmanagedProductResource::COLUMN_ACCOUNT_ID,
            $accountId
        );

        return array_values($collection->getItems());
    }

    /**
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param int $accountId
     *
     * @return \M2E\Temu\Model\UnmanagedProduct[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function findForAutoMappingByMassActionSelectedProducts(MassActionFilter $filter, int $accountId): array
    {
        $collection = $this->collectionUnmanagedFactory->create();
        $filter->getCollection($collection);

        $collection->addFieldToFilter(
            UnmanagedProductResource::COLUMN_MAGENTO_PRODUCT_ID,
            ['null' => true]
        );

        $collection->addFieldToFilter(
            UnmanagedProductResource::COLUMN_ACCOUNT_ID,
            $accountId
        );

        return array_values($collection->getItems());
    }
}
