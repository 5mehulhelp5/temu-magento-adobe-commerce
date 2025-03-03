<?php

namespace M2E\Temu\Model\Policy\SellingFormat;

class Repository
{
    private \M2E\Temu\Model\ResourceModel\Policy\SellingFormat $resource;
    private \M2E\Temu\Model\Policy\SellingFormatFactory $sellingFormatFactory;
    private \M2E\Temu\Model\ResourceModel\Policy\SellingFormat\CollectionFactory $collectionFactory;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\Policy\SellingFormat $resource,
        \M2E\Temu\Model\ResourceModel\Policy\SellingFormat\CollectionFactory $collectionFactory,
        \M2E\Temu\Model\Policy\SellingFormatFactory $sellingFormatFactory
    ) {
        $this->resource = $resource;
        $this->sellingFormatFactory = $sellingFormatFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function find(int $id): ?\M2E\Temu\Model\Policy\SellingFormat
    {
        $model = $this->sellingFormatFactory->create();
        $this->resource->load($model, $id);

        if ($model->isObjectNew()) {
            return null;
        }

        return $model;
    }

    /**
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    public function get(int $id): \M2E\Temu\Model\Policy\SellingFormat
    {
        $template = $this->find($id);
        if ($template === null) {
            throw new \M2E\Temu\Model\Exception\Logic('Synchronization not found');
        }

        return $template;
    }

    public function delete(\M2E\Temu\Model\Policy\SellingFormat $template)
    {
        $this->resource->delete($template);
    }

    public function create(\M2E\Temu\Model\Policy\SellingFormat $template)
    {
        $this->resource->save($template);
    }

    public function save(\M2E\Temu\Model\Policy\SellingFormat $template)
    {
        $this->resource->save($template);
    }

    /**
     * @return \M2E\Temu\Model\Policy\SellingFormat[]
     */
    public function getAll(): array
    {
        $collection = $this->collectionFactory->create();

        return array_values($collection->getItems());
    }
}
