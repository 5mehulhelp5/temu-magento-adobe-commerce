<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product;

class DeleteService
{
    private \M2E\Temu\Model\Tag\ListingProduct\Buffer $tagBuffer;
    private \M2E\Temu\Model\Product\Repository $listingProductRepository;
    private \M2E\Temu\Model\ScheduledAction\Repository $scheduledActionRepository;
    private \M2E\Temu\Model\Instruction\Repository $instructionRepository;
    private \M2E\Temu\Model\Listing\LogService $listingLogService;
    private VariantSku\DeletedVariantSkuService $backupVariantSkuService;

    public function __construct(
        \M2E\Temu\Model\Tag\ListingProduct\Buffer $tagBuffer,
        \M2E\Temu\Model\Product\Repository $listingProductRepository,
        \M2E\Temu\Model\ScheduledAction\Repository $scheduledActionRepository,
        \M2E\Temu\Model\Instruction\Repository $instructionRepository,
        \M2E\Temu\Model\Listing\LogService $listingLogService,
        VariantSku\DeletedVariantSkuService $backupVariantSkuService
    ) {
        $this->tagBuffer = $tagBuffer;
        $this->listingProductRepository = $listingProductRepository;
        $this->scheduledActionRepository = $scheduledActionRepository;
        $this->instructionRepository = $instructionRepository;
        $this->listingLogService = $listingLogService;
        $this->backupVariantSkuService = $backupVariantSkuService;
    }

    public function process(
        \M2E\Temu\Model\Product $product,
        $initiator
    ): void {
        $this->removeTags($product);

        $this->removeScheduledActions($product);
        $this->removeInstructions($product);

        $this->listingLogService->addProduct(
            $product,
            $initiator,
            \M2E\Temu\Model\Listing\Log::ACTION_DELETE_PRODUCT_FROM_LISTING,
            $this->listingLogService->getNextActionId(),
            (string)__('Product was Deleted'),
            \M2E\Temu\Model\Log\AbstractModel::TYPE_INFO,
        );

        $this->backupVariantSkuService->deleteForProduct($product);
        foreach ($product->getVariants() as $variant) {
            $this->listingProductRepository->deleteVariantSku($variant);
        }

        $this->listingProductRepository->delete($product);
    }

    private function removeTags(\M2E\Temu\Model\Product $product): void
    {
        $this->tagBuffer->removeAllTags($product);
        $this->tagBuffer->flush();
    }

    private function removeScheduledActions(\M2E\Temu\Model\Product $product): void
    {
        $scheduledAction = $this->scheduledActionRepository->findByListingProductId($product->getId());
        if ($scheduledAction !== null) {
            $this->scheduledActionRepository->remove($scheduledAction);
        }
    }

    private function removeInstructions(\M2E\Temu\Model\Product $product): void
    {
        $this->instructionRepository->removeByListingProduct($product->getId());
    }
}
