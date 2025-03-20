<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Attribute;

use M2E\Temu\Model\Category\CategoryAttribute;
use M2E\Temu\Model\ResourceModel\Category\Attribute as AttributeResource;

class Repository
{
    private \M2E\Temu\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeCollectionFactory;
    private AttributeResource $attributeResource;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeCollectionFactory,
        AttributeResource $attributeResource
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->attributeResource = $attributeResource;
    }

    public function create(CategoryAttribute $entity): void
    {
        $this->attributeResource->save($entity);
    }

    public function save(CategoryAttribute $attrEntity): void
    {
        $this->attributeResource->save($attrEntity);
    }

    public function delete(CategoryAttribute $attrEntity): void
    {
        $this->attributeResource->delete($attrEntity);
    }

    /**
     * @return CategoryAttribute[]
     */
    public function findByDictionaryId(
        int $dictionaryId,
        array $typeFilter = []
    ): array {
        $collection = $this->attributeCollectionFactory->create();
        $collection->addFieldToFilter(
            AttributeResource::COLUMN_CATEGORY_DICTIONARY_ID,
            ['eq' => $dictionaryId]
        );

        if ($typeFilter !== []) {
            $collection->addFieldToFilter(
                AttributeResource::COLUMN_ATTRIBUTE_TYPE,
                ['in' => $typeFilter]
            );
        }

        return array_values($collection->getItems());
    }

    public function getCountByDictionaryId(int $dictionaryId): int
    {
        $collection = $this->attributeCollectionFactory->create();
        $collection->addFieldToFilter(
            AttributeResource::COLUMN_CATEGORY_DICTIONARY_ID,
            $dictionaryId
        );

        return $collection->getSize();
    }

    /**
     * @return CategoryAttribute[]
     */
    public function findByDictionaryIdAndAttributeIds(
        int $dictionaryId,
        array $attributeIds
    ): array {
        $collection = $this->attributeCollectionFactory->create();
        $collection->addFieldToFilter(
            AttributeResource::COLUMN_CATEGORY_DICTIONARY_ID,
            ['eq' => $dictionaryId]
        );
        $collection->addFieldToFilter(
            AttributeResource::COLUMN_ATTRIBUTE_ID,
            ['in' => $attributeIds]
        );

        return array_values($collection->getItems());
    }
}
