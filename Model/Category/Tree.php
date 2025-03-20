<?php

namespace M2E\Temu\Model\Category;

use M2E\Temu\Model\ResourceModel\Category\Tree as CategoryTreeResource;

class Tree extends \M2E\Temu\Model\ActiveRecord\AbstractModel
{
    public const PERMISSION_STATUSES_NON_MAIN_CATEGORY = 'NON_MAIN_CATEGORY';
    public const PERMISSION_STATUSES_INVITE_ONLY = 'INVITE_ONLY';
    public const PERMISSION_STATUSES_AVAILABLE = 'AVAILABLE';

    public function _construct(): void
    {
        parent::_construct();
        $this->_init(CategoryTreeResource::class);
    }

    public function create(
        string $region,
        int $categoryId,
        ?int $parentCategoryId,
        string $title,
        bool $isLeaf
    ): self {
        $this->setData(CategoryTreeResource::COLUMN_REGION, $region);
        $this->setData(CategoryTreeResource::COLUMN_CATEGORY_ID, $categoryId);
        $this->setData(CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID, $parentCategoryId);
        $this->setData(CategoryTreeResource::COLUMN_TITLE, $title);
        $this->setData(CategoryTreeResource::COLUMN_IS_LEAF, $isLeaf);

        return $this;
    }

    public function getRegion(): string
    {
        return $this->getData(CategoryTreeResource::COLUMN_REGION);
    }

    public function getCategoryId(): int
    {
        return (int)$this->getData(CategoryTreeResource::COLUMN_CATEGORY_ID);
    }

    public function getTitle(): string
    {
        return $this->getData(CategoryTreeResource::COLUMN_TITLE);
    }

    public function isLeaf(): bool
    {
        return (bool)$this->getData(CategoryTreeResource::COLUMN_IS_LEAF);
    }

    public function getParentCategoryId(): ?int
    {
        $parentCategoryId = $this->getDataByKey(CategoryTreeResource::COLUMN_PARENT_CATEGORY_ID);

        return $parentCategoryId ? (int)$parentCategoryId : null;
    }

    public function getPermissionStatuses(): array //TODO remove
    {
        $permissionStatuses = $this->getData(CategoryTreeResource::COLUMN_PERMISSION_STATUSES);
        if ($permissionStatuses === null) {
            return [];
        }

        return json_decode($permissionStatuses, true);
    }

    public function isInviteOnly(): bool //TODO remove
    {
        foreach ($this->getPermissionStatuses() as $permissionStatus) {
            if ($permissionStatus === self::PERMISSION_STATUSES_INVITE_ONLY) {
                return true;
            }
        }

        return false;
    }
}
