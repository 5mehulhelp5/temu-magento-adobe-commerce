<?php

namespace M2E\Temu\Model\Category\Tree;

use M2E\Temu\Model\Category\Tree;
use M2E\Temu\Model\ResourceModel\Category\Tree as CategoryTreeResource;

class Repository
{
    private CategoryTreeResource\CollectionFactory $collectionFactory;
    /** @var \M2E\Temu\Model\ResourceModel\Category\Tree */
    private CategoryTreeResource $categoryTreeResource;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\Category\Tree\CollectionFactory $collectionFactory,
        CategoryTreeResource $categoryTreeResource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->categoryTreeResource = $categoryTreeResource;
    }

    /**
     * @return Tree[]
     */
    public function getRootCategories(string $region): array
    {
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(
            CategoryTreeResource::COLUMN_REGION,
            ['eq' => $region]
        );
        $collection->addFieldToFilter(
            CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID,
            ['null' => true]
        );

        return array_values($collection->getItems());
    }

    public function getCategoryByRegionAndCategoryId(string $region, int $categoryId): ?Tree
    {
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(
            CategoryTreeResource::COLUMN_REGION,
            ['eq' => $region]
        );
        $collection->addFieldToFilter(
            CategoryTreeResource::COLUMN_CATEGORY_ID,
            ['eq' => $categoryId]
        );

        /** @var Tree $entity */
        $entity = $collection->getFirstItem();

        if ($entity->isObjectNew()) {
            return null;
        }

        return $entity;
    }

    /**
     * @param string $region
     * @param int $parentCategoryId
     *
     * @return Tree[]
     */
    public function getChildCategories(string $region, int $parentCategoryId): array
    {
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(
            CategoryTreeResource::COLUMN_REGION,
            ['eq' => $region]
        );
        $collection->addFieldToFilter(
            CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID,
            ['eq' => $parentCategoryId]
        );

        return array_values($collection->getItems());
    }

    /**
     * @param Tree $entity
     *
     * @return Tree[]
     */
    public function getParents(Tree $entity): array
    {
        $ancestors = $this->getRecursivelyParents($entity);

        return array_reverse($ancestors);
    }

    /**
     * @param Tree[] $ancestors
     *
     * @return Tree[]
     */
    private function getRecursivelyParents(Tree $child, array $ancestors = []): array
    {
        if ($child->getParentCategoryId() === null) {
            return $ancestors;
        }

        $parent = $this->getCategoryByRegionAndCategoryId(
            $child->getRegion(),
            $child->getParentCategoryId()
        );
        if ($parent === null) {
            return $ancestors;
        }

        $ancestors[] = $parent;

        return $this->getRecursivelyParents($parent, $ancestors);
    }

    /**
     * @param \M2E\Temu\Model\Category\Tree[] $categories
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function batchInsert(array $categories): void
    {
        $insertData = [];
        foreach ($categories as $category) {
            $insertData[] = [
                CategoryTreeResource::COLUMN_REGION => $category->getRegion(),
                CategoryTreeResource::COLUMN_CATEGORY_ID => $category->getCategoryId(),
                CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID => $category->getParentCategoryId(),
                CategoryTreeResource::COLUMN_TITLE => $category->getTitle(),
                CategoryTreeResource::COLUMN_IS_LEAF => $category->isLeaf(),
                CategoryTreeResource::COLUMN_PERMISSION_STATUSES => json_encode($category->getPermissionStatuses()),
            ];
        }

        $collection = $this->collectionFactory->create();
        $resource = $collection->getResource();

        foreach (array_chunk($insertData, 500) as $chunk) {
            $resource->getConnection()->insertMultiple($resource->getMainTable(), $chunk);
        }
    }

    public function deleteByRegionAndParentCategoryId(string $region, ?int $parentCategoryId = null): void
    {
        $collection = $this->collectionFactory->create();
        $connection = $collection->getConnection();

        $conditions = [
            sprintf('%s = %s', CategoryTreeResource::COLUMN_REGION, $connection->quote($region)),
        ];

        if ($parentCategoryId === null) {
            $conditions[] = sprintf('%s IS NULL', CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID);
        } else {
            $conditions[] = sprintf(
                '%s = %d',
                CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID,
                $connection->quote($parentCategoryId)
            );
        }

        $connection->delete(
            $collection->getMainTable(),
            implode(' AND ', $conditions)
        );
    }

    /**
     * @return Tree[]
     */
    public function searchByTitleOrId(string $region, string $query, int $limit): array
    {
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(
            CategoryTreeResource::COLUMN_REGION,
            ['eq' => $region]
        );

        $collection->addFieldToFilter(
            [CategoryTreeResource::COLUMN_TITLE, CategoryTreeResource::COLUMN_CATEGORY_ID],
            [['like' => "%$query%"], ['like' => "%$query%"]]
        );

        $collection->getSelect()->order([
            sprintf('%s DESC', CategoryTreeResource::COLUMN_IS_LEAF),
            CategoryTreeResource::COLUMN_CATEGORY_ID,
            CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID,
        ]);

        $collection->setPageSize($limit);

        return array_values($collection->getItems());
    }

    /**
     * @return Tree[]
     */
    public function getChildren(string $region, int $parentCategoryId): array
    {
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(
            CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID,
            ['eq' => $parentCategoryId]
        );

        $collection->addFieldToFilter(
            CategoryTreeResource::COLUMN_REGION,
            ['eq' => $region]
        );

        $collection->getSelect()->order([
            CategoryTreeResource::COLUMN_CATEGORY_ID,
            CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID,
        ]);

        return array_values($collection->getItems());
    }

    public function categoryTreeExists(string $region, ?int $categoryId = null): bool
    {
        $connection = $this->categoryTreeResource->getConnection();
        $tableName = $this->categoryTreeResource->getMainTable();

        $select = $connection->select()
                             ->from($tableName, [new \Zend_Db_Expr('1')])
                             ->where(CategoryTreeResource::COLUMN_REGION . ' = :region_code')
                             ->limit(1);

        $bind = [
            ':region_code' => $region,
        ];

        if ($categoryId) {
            $select->where(CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID . ' = :category_id');
            $bind[':category_id'] = $categoryId;
        } else {
            $select->where(CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID . ' IS NULL');
        }

        $result = $connection->fetchOne($select, $bind);

        return (bool)$result;
    }
}
