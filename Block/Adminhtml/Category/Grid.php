<?php

namespace M2E\Temu\Block\Adminhtml\Category;

class Grid extends \M2E\Temu\Block\Adminhtml\Magento\Grid\AbstractGrid
{
    protected $categoryCollectionFactory;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\Magento\Category\CollectionFactory $categoryCollectionFactory,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function getStoreId()
    {
        return $this->getData('store_id') !== null
            ? $this->getData('store_id') : \Magento\Store\Model\Store::DISTRO_STORE_ID;
    }

    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);

        return $this;
    }

    public function setCollection($collection)
    {
        $this->_prepareCache(clone $collection);
        parent::setCollection($collection);
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     */
    protected function _prepareCache($collection)
    {
        $stmt = $collection->getSelect()->query();

        $ids = [];
        foreach ($stmt as $item) {
            $ids = array_merge($ids, array_map('intval', explode('/', $item['path'])));
        }
        $ids = array_unique($ids);

        if (empty($ids)) {
            return;
        }

        $collection = $this->categoryCollectionFactory->create();
        $collection->joinAttribute(
            'name',
            'catalog_category/name',
            'entity_id',
            null,
            'inner',
            $this->getStoreId()
        );
        $collection->addFieldToFilter([
            ['attribute' => 'entity_id', 'in' => $ids],
        ]);

        $cacheData = [];
        foreach ($collection->getItems() as $item) {
            /** @var \Magento\Catalog\Model\Category $item */
            $cacheData[$item->getData('entity_id')] = $item->getData('name');
        }
        $this->setData('categories_cache', $cacheData);
    }

    public function callbackColumnMagentoCategory($value, $row, $column, $isExport)
    {
        $ids = explode('/', $row->getPath());

        $categoriesCache = $this->getData('categories_cache');
        $path = '';
        foreach ($ids as $id) {
            if (!isset($categoriesCache[$id])) {
                continue;
            }
            $path != '' && $path .= ' > ';
            $path .= $categoriesCache[$id];
        }

        return \M2E\Core\Helper\Data::escapeHtml($path);
    }

    public function getMultipleRows($item)
    {
        return false;
    }
}
