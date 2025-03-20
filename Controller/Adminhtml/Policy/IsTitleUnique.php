<?php

namespace M2E\Temu\Controller\Adminhtml\Policy;

use M2E\Temu\Controller\Adminhtml\AbstractTemplate;

class IsTitleUnique extends AbstractTemplate
{
    private \M2E\Temu\Model\ResourceModel\Policy\Synchronization\CollectionFactory $syncCollectionFactory;
    private \M2E\Temu\Model\ResourceModel\Policy\SellingFormat\CollectionFactory $sellingCollectionFactory;
    private \M2E\Temu\Model\ResourceModel\Policy\Description\CollectionFactory $descriptionCollectionFactory;
    private \M2E\Temu\Model\ResourceModel\Policy\Shipping\CollectionFactory $shippingCollectionFactory;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\Policy\Synchronization\CollectionFactory $syncCollectionFactory,
        \M2E\Temu\Model\ResourceModel\Policy\SellingFormat\CollectionFactory $sellingCollectionFactory,
        \M2E\Temu\Model\ResourceModel\Policy\Description\CollectionFactory $descriptionCollectionFactory,
        \M2E\Temu\Model\ResourceModel\Policy\Shipping\CollectionFactory $shippingCollectionFactory,
        \M2E\Temu\Model\Policy\Manager $templateManager,
        \M2E\Temu\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($templateManager);
        $this->syncCollectionFactory = $syncCollectionFactory;
        $this->sellingCollectionFactory = $sellingCollectionFactory;
        $this->descriptionCollectionFactory = $descriptionCollectionFactory;
        $this->shippingCollectionFactory = $shippingCollectionFactory;
    }

    public function execute()
    {
        $nick = $this->getRequest()->getParam('nick');
        $ignoreId = $this->getRequest()->getParam('id_value');
        $title = $this->getRequest()->getParam('title');

        if ($title == '') {
            $this->setJsonContent(['unique' => false]);

            return $this->getResult();
        }

        if ($nick === \M2E\Temu\Model\Policy\Manager::TEMPLATE_SYNCHRONIZATION) {
            return $this->isUniqueTitleSynchronizationTemplate($ignoreId, $title);
        }

        if ($nick === \M2E\Temu\Model\Policy\Manager::TEMPLATE_SELLING_FORMAT) {
            return $this->isUniqueTitleSellingFormatTemplate($ignoreId, $title);
        }

        if ($nick === \M2E\Temu\Model\Policy\Manager::TEMPLATE_DESCRIPTION) {
            return $this->isUniqueTitleDescriptionTemplate($ignoreId, $title);
        }

        if ($nick === \M2E\Temu\Model\Policy\Manager::TEMPLATE_SHIPPING) {
            return $this->isUniqueTitleShippingTemplate($ignoreId, $title);
        }

        throw new \M2E\Temu\Model\Exception\Logic('Unknown nick ' . $nick);
    }

    private function isUniqueTitleSynchronizationTemplate($ignoreId, $title)
    {
        $collection = $this->syncCollectionFactory
            ->create()
            ->addFieldToFilter(
                \M2E\Temu\Model\ResourceModel\Policy\Synchronization::COLUMN_IS_CUSTOM_TEMPLATE,
                0
            )
            ->addFieldToFilter(
                \M2E\Temu\Model\ResourceModel\Policy\Synchronization::COLUMN_TITLE,
                $title
            );

        if ($ignoreId) {
            $collection->addFieldToFilter(
                \M2E\Temu\Model\ResourceModel\Policy\Synchronization::COLUMN_ID,
                ['neq' => $ignoreId]
            );
        }

        $this->setJsonContent(['unique' => $collection->getSize() === 0]);

        return $this->getResult();
    }

    private function isUniqueTitleSellingFormatTemplate($ignoreId, $title)
    {
        $collection = $this->sellingCollectionFactory
            ->create()
            ->addFieldToFilter(
                \M2E\Temu\Model\ResourceModel\Policy\SellingFormat::COLUMN_IS_CUSTOM_TEMPLATE,
                0
            )
            ->addFieldToFilter(
                \M2E\Temu\Model\ResourceModel\Policy\SellingFormat::COLUMN_TITLE,
                $title
            );

        if ($ignoreId) {
            $collection->addFieldToFilter(
                \M2E\Temu\Model\ResourceModel\Policy\SellingFormat::COLUMN_ID,
                ['neq' => $ignoreId]
            );
        }

        $this->setJsonContent(['unique' => $collection->getSize() === 0]);

        return $this->getResult();
    }

    private function isUniqueTitleDescriptionTemplate($ignoreId, $title)
    {
        $collection = $this->descriptionCollectionFactory
            ->create()
            ->addFieldToFilter(
                \M2E\Temu\Model\ResourceModel\Policy\Description::COLUMN_IS_CUSTOM_TEMPLATE,
                0
            )
            ->addFieldToFilter(
                \M2E\Temu\Model\ResourceModel\Policy\Description::COLUMN_TITLE,
                $title
            );

        if ($ignoreId) {
            $collection->addFieldToFilter(
                \M2E\Temu\Model\ResourceModel\Policy\Description::COLUMN_ID,
                ['neq' => $ignoreId]
            );
        }

        $this->setJsonContent(['unique' => $collection->getSize() === 0]);

        return $this->getResult();
    }

    private function isUniqueTitleShippingTemplate($ignoreId, $title)
    {
        $collection = $this->shippingCollectionFactory
            ->create()
            ->addFieldToFilter(
                \M2E\Temu\Model\ResourceModel\Policy\Shipping::COLUMN_TITLE,
                $title
            );

        if ($ignoreId) {
            $collection->addFieldToFilter(
                \M2E\Temu\Model\ResourceModel\Policy\Shipping::COLUMN_ID,
                ['neq' => $ignoreId]
            );
        }

        $this->setJsonContent(['unique' => $collection->getSize() === 0]);

        return $this->getResult();
    }
}
