<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Listing;

use M2E\Temu\Model\Policy\SellingFormat;
use M2E\Temu\Model\Policy\Synchronization;
use M2E\Temu\Model\Policy\Description;
use M2E\Temu\Model\Policy\Shipping;
use M2E\Temu\Model\ResourceModel\Listing as ListingResource;

class UpdateService
{
    private \M2E\Temu\Model\Listing\SnapshotBuilderFactory $listingSnapshotBuilderFactory;
    private \M2E\Temu\Model\Listing\Repository $listingRepository;
    private \M2E\Temu\Model\Listing\AffectedListingsProductsFactory $affectedListingsProductsFactory;
    private SellingFormat\Repository $sellingFormatTemplateRepository;
    private SellingFormat\SnapshotBuilderFactory $sellingFormatSnapshotBuilderFactory;
    private SellingFormat\DiffFactory $sellingFormatDiffFactory;
    private SellingFormat\ChangeProcessorFactory $sellingFormatChangeProcessorFactory;
    private Synchronization\Repository $synchronizationTemplateRepository;
    private Synchronization\SnapshotBuilderFactory $synchronizationSnapshotBuilderFactory;
    private Synchronization\DiffFactory $synchronizationDiffFactory;
    private Synchronization\ChangeProcessorFactory $synchronizationChangeProcessorFactory;
    private \M2E\Temu\Model\Policy\Description\Repository $descriptionTemplateRepository;
    private \M2E\Temu\Model\Policy\Description\SnapshotBuilderFactory $descriptionSnapshotBuilderFactory;
    private \M2E\Temu\Model\Policy\Description\DiffFactory $descriptionDiffFactory;
    private \M2E\Temu\Model\Policy\Description\ChangeProcessorFactory $descriptionChangeProcessorFactory;
    private \M2E\Temu\Model\Policy\Shipping\Repository $shippingTemplateRepository;
    private \M2E\Temu\Model\Policy\Shipping\SnapshotBuilderFactory $shippingSnapshotBuilderFactory;
    private \M2E\Temu\Model\Policy\Shipping\DiffFactory $shippingDiffFactory;
    private \M2E\Temu\Model\Policy\Shipping\ChangeProcessorFactory $shippingChangeProcessorFactory;

    public function __construct(
        \M2E\Temu\Model\Listing\Repository $listingRepository,
        \M2E\Temu\Model\Listing\SnapshotBuilderFactory $listingSnapshotBuilderFactory,
        \M2E\Temu\Model\Listing\AffectedListingsProductsFactory $affectedListingsProductsFactory,
        SellingFormat\Repository $sellingFormatTemplateRepository,
        SellingFormat\SnapshotBuilderFactory $sellingFormatSnapshotBuilderFactory,
        SellingFormat\DiffFactory $sellingFormatDiffFactory,
        SellingFormat\ChangeProcessorFactory $sellingFormatChangeProcessorFactory,
        Synchronization\Repository $synchronizationTemplateRepository,
        Synchronization\SnapshotBuilderFactory $synchronizationSnapshotBuilderFactory,
        Synchronization\DiffFactory $synchronizationDiffFactory,
        Synchronization\ChangeProcessorFactory $synchronizationChangeProcessorFactory,
        Description\Repository $descriptionTemplateRepository,
        Description\SnapshotBuilderFactory $descriptionSnapshotBuilderFactory,
        Description\DiffFactory $descriptionDiffFactory,
        Description\ChangeProcessorFactory $descriptionChangeProcessorFactory,
        Shipping\Repository $shippingTemplateRepository,
        Shipping\SnapshotBuilderFactory $shippingSnapshotBuilderFactory,
        Shipping\DiffFactory $shippingDiffFactory,
        Shipping\ChangeProcessorFactory $shippingChangeProcessorFactory
    ) {
        $this->listingSnapshotBuilderFactory = $listingSnapshotBuilderFactory;
        $this->listingRepository = $listingRepository;
        $this->affectedListingsProductsFactory = $affectedListingsProductsFactory;
        $this->sellingFormatTemplateRepository = $sellingFormatTemplateRepository;
        $this->sellingFormatSnapshotBuilderFactory = $sellingFormatSnapshotBuilderFactory;
        $this->sellingFormatDiffFactory = $sellingFormatDiffFactory;
        $this->sellingFormatChangeProcessorFactory = $sellingFormatChangeProcessorFactory;
        $this->synchronizationTemplateRepository = $synchronizationTemplateRepository;
        $this->synchronizationSnapshotBuilderFactory = $synchronizationSnapshotBuilderFactory;
        $this->synchronizationDiffFactory = $synchronizationDiffFactory;
        $this->synchronizationChangeProcessorFactory = $synchronizationChangeProcessorFactory;
        $this->descriptionTemplateRepository = $descriptionTemplateRepository;
        $this->descriptionSnapshotBuilderFactory = $descriptionSnapshotBuilderFactory;
        $this->descriptionDiffFactory = $descriptionDiffFactory;
        $this->descriptionChangeProcessorFactory = $descriptionChangeProcessorFactory;
        $this->shippingTemplateRepository = $shippingTemplateRepository;
        $this->shippingSnapshotBuilderFactory = $shippingSnapshotBuilderFactory;
        $this->shippingDiffFactory = $shippingDiffFactory;
        $this->shippingChangeProcessorFactory = $shippingChangeProcessorFactory;
    }

    /**
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    public function update(\M2E\Temu\Model\Listing $listing, array $post)
    {
        $isNeedProcessChangesSellingFormatTemplate = false;
        $isNeedProcessChangesSynchronizationTemplate = false;
        $isNeedProcessChangesDescriptionTemplate = false;
        $isNeedProcessChangesShippingTemplate = false;

        $oldListingSnapshot = $this->makeListingSnapshot($listing);

        $newTemplateSellingFormatId = $post[ListingResource::COLUMN_TEMPLATE_SELLING_FORMAT_ID] ?? null;
        if (
            $newTemplateSellingFormatId !== null
            && $listing->getTemplateSellingFormatId() !== (int)$newTemplateSellingFormatId
        ) {
            $listing->setTemplateSellingFormatId((int)$newTemplateSellingFormatId);
            $isNeedProcessChangesSellingFormatTemplate = true;
        }

        $newTemplateSynchronizationId = $post[ListingResource::COLUMN_TEMPLATE_SYNCHRONIZATION_ID] ?? null;
        if (
            $newTemplateSynchronizationId !== null
            && $listing->getTemplateSynchronizationId() !== (int)$newTemplateSynchronizationId
        ) {
            $listing->setTemplateSynchronizationId((int)$newTemplateSynchronizationId);
            $isNeedProcessChangesSynchronizationTemplate = true;
        }

        $newTemplateDescriptionId = $post[ListingResource::COLUMN_TEMPLATE_DESCRIPTION_ID] ?? null;
        if (
            $newTemplateDescriptionId !== null
            && $listing->getTemplateDescriptionId() !== (int)$newTemplateDescriptionId
        ) {
            $listing->setTemplateDescriptionId((int)$newTemplateDescriptionId);
            $isNeedProcessChangesDescriptionTemplate = true;
        }

        $newTemplateShippingId = !empty($post[ListingResource::COLUMN_TEMPLATE_SHIPPING_ID] ?? null)
            ? (int)$post[ListingResource::COLUMN_TEMPLATE_SHIPPING_ID]
            : null;
        if (
            $newTemplateShippingId !== null
            && $listing->getTemplateShippingId() !== $newTemplateShippingId
        ) {
            $listing->setTemplateShippingId($newTemplateShippingId);
            $isNeedProcessChangesShippingTemplate = true;
        }

        if (
            $isNeedProcessChangesSellingFormatTemplate === false
            && $isNeedProcessChangesSynchronizationTemplate === false
            && $isNeedProcessChangesDescriptionTemplate === false
            && $isNeedProcessChangesShippingTemplate === false
        ) {
            return;
        }

        $this->listingRepository->save($listing);

        $newListingSnapshot = $this->makeListingSnapshot($listing);

        $affectedListingsProducts = $this->affectedListingsProductsFactory->create();
        $affectedListingsProducts->setModel($listing);

        if ($isNeedProcessChangesSellingFormatTemplate) {
            $this->processChangeSellingFormatTemplate(
                (int)$oldListingSnapshot[ListingResource::COLUMN_TEMPLATE_SELLING_FORMAT_ID],
                (int)$newListingSnapshot[ListingResource::COLUMN_TEMPLATE_SELLING_FORMAT_ID],
                $affectedListingsProducts
            );
        }

        if ($isNeedProcessChangesSynchronizationTemplate) {
            $this->processChangeSynchronizationTemplate(
                (int)$oldListingSnapshot[ListingResource::COLUMN_TEMPLATE_SYNCHRONIZATION_ID],
                (int)$newListingSnapshot[ListingResource::COLUMN_TEMPLATE_SYNCHRONIZATION_ID],
                $affectedListingsProducts
            );
        }

        if ($isNeedProcessChangesDescriptionTemplate) {
            $this->processChangeDescriptionTemplate(
                (int)$oldListingSnapshot[ListingResource::COLUMN_TEMPLATE_DESCRIPTION_ID],
                (int)$newListingSnapshot[ListingResource::COLUMN_TEMPLATE_DESCRIPTION_ID],
                $affectedListingsProducts
            );
        }

        if ($isNeedProcessChangesShippingTemplate) {
            $this->processChangeShippingTemplate(
                (int)$oldListingSnapshot[ListingResource::COLUMN_TEMPLATE_SHIPPING_ID],
                (int)$newListingSnapshot[ListingResource::COLUMN_TEMPLATE_SHIPPING_ID],
                $affectedListingsProducts
            );
        }
    }

    private function makeListingSnapshot(\M2E\Temu\Model\Listing $listing): array
    {
        $snapshotBuilder = $this->listingSnapshotBuilderFactory->create();
        $snapshotBuilder->setModel($listing);

        return $snapshotBuilder->getSnapshot();
    }

    /**
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    private function processChangeSellingFormatTemplate(
        int $oldId,
        int $newId,
        \M2E\Temu\Model\Listing\AffectedListingsProducts $affectedListingsProducts
    ) {
        $oldTemplate = $this->sellingFormatTemplateRepository->get($oldId);
        $newTemplate = $this->sellingFormatTemplateRepository->get($newId);

        $oldTemplateData = $this->makeSellingFormatTemplateSnapshot($oldTemplate);
        $newTemplateData = $this->makeSellingFormatTemplateSnapshot($newTemplate);

        $diff = $this->sellingFormatDiffFactory->create();
        $diff->setOldSnapshot($oldTemplateData);
        $diff->setNewSnapshot($newTemplateData);

        $changeProcessor = $this->sellingFormatChangeProcessorFactory->create();

        $affectedProducts = $affectedListingsProducts->getObjectsData(['id', 'status']);
        $changeProcessor->process($diff, $affectedProducts);
    }

    private function makeSellingFormatTemplateSnapshot(SellingFormat $sellingFormatTemplate): array
    {
        $snapshotBuilder = $this->sellingFormatSnapshotBuilderFactory->create();
        $snapshotBuilder->setModel($sellingFormatTemplate);

        return $snapshotBuilder->getSnapshot();
    }

    /**
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    private function processChangeSynchronizationTemplate(
        int $oldId,
        int $newId,
        \M2E\Temu\Model\Listing\AffectedListingsProducts $affectedListingsProducts
    ) {
        $oldTemplate = $this->synchronizationTemplateRepository->get($oldId);
        $newTemplate = $this->synchronizationTemplateRepository->get($newId);

        $oldTemplateData = $this->makeSynchronizationTemplateSnapshot($oldTemplate);
        $newTemplateData = $this->makeSynchronizationTemplateSnapshot($newTemplate);

        $diff = $this->synchronizationDiffFactory->create();
        $diff->setOldSnapshot($oldTemplateData);
        $diff->setNewSnapshot($newTemplateData);

        $changeProcessor = $this->synchronizationChangeProcessorFactory->create();

        $affectedProducts = $affectedListingsProducts->getObjectsData(['id', 'status']);
        $changeProcessor->process($diff, $affectedProducts);
    }

    private function makeSynchronizationTemplateSnapshot(Synchronization $synchronizationTemplate): array
    {
        $snapshotBuilder = $this->synchronizationSnapshotBuilderFactory->create();
        $snapshotBuilder->setModel($synchronizationTemplate);

        return $snapshotBuilder->getSnapshot();
    }

    private function processChangeDescriptionTemplate(
        int $oldId,
        int $newId,
        \M2E\Temu\Model\Listing\AffectedListingsProducts $affectedListingsProducts
    ) {
        $oldTemplate = $this->descriptionTemplateRepository->find($oldId);
        $newTemplate = $this->descriptionTemplateRepository->get($newId);

        if ($oldTemplate === null) {
            $oldTemplateData = [];
        } else {
            $oldTemplateData = $this->makeDescriptionTemplateSnapshot($oldTemplate);
        }

        $newTemplateData = $this->makeDescriptionTemplateSnapshot($newTemplate);

        $diff = $this->descriptionDiffFactory->create();
        $diff->setOldSnapshot($oldTemplateData);
        $diff->setNewSnapshot($newTemplateData);

        $changeProcessor = $this->descriptionChangeProcessorFactory->create();

        $affectedProducts = $affectedListingsProducts->getObjectsData(
            ['id', 'status'],
            ['template' => \M2E\Temu\Model\Policy\Manager::TEMPLATE_DESCRIPTION]
        );
        $changeProcessor->process($diff, $affectedProducts);
    }

    private function makeDescriptionTemplateSnapshot(Description $descriptionTemplate)
    {
        $snapshotBuilder = $this->descriptionSnapshotBuilderFactory->create();
        $snapshotBuilder->setModel($descriptionTemplate);

        return $snapshotBuilder->getSnapshot();
    }

    private function processChangeShippingTemplate(
        int $oldId,
        int $newId,
        \M2E\Temu\Model\Listing\AffectedListingsProducts $affectedListingsProducts
    ) {
        $oldTemplateData = [];
        $newTemplateData = [];

        if (!empty($oldId)) {
            $oldTemplate = $this->shippingTemplateRepository->get($oldId);
            $oldTemplateData = $this->makeShippingTemplateSnapshot($oldTemplate);
        }

        if (!empty($newId)) {
            $newTemplate = $this->shippingTemplateRepository->get($newId);
            $newTemplateData = $this->makeShippingTemplateSnapshot($newTemplate);
        }

        $diff = $this->shippingDiffFactory->create();
        $diff->setOldSnapshot($oldTemplateData);
        $diff->setNewSnapshot($newTemplateData);

        $changeProcessor = $this->shippingChangeProcessorFactory->create();

        $affectedProducts = $affectedListingsProducts->getObjectsData(['id', 'status']);
        $changeProcessor->process($diff, $affectedProducts);
    }

    private function makeShippingTemplateSnapshot(Shipping $shippingTemplate): array
    {
        $snapshotBuilder = $this->shippingSnapshotBuilderFactory->create();
        $snapshotBuilder->setModel($shippingTemplate);

        return $snapshotBuilder->getSnapshot();
    }
}
