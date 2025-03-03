<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Listing;

class ChangeStoreService
{
    private \M2E\Temu\Model\Product\Repository $productRepository;
    private \M2E\Temu\Model\Listing\Repository $listingRepository;
    private \M2E\Temu\Model\InstructionService $instructionService;

    public function __construct(
        \M2E\Temu\Model\Product\Repository $productRepository,
        \M2E\Temu\Model\Listing\Repository $listingRepository,
        \M2E\Temu\Model\InstructionService $instructionService
    ) {
        $this->productRepository = $productRepository;
        $this->listingRepository = $listingRepository;
        $this->instructionService = $instructionService;
    }

    public function change(\M2E\Temu\Model\Listing $listing, int $storeId): void
    {
        $this->updateStoreViewInListing($listing, $storeId);
        $this->addInstruction($listing->getId());
    }

    private function updateStoreViewInListing(\M2E\Temu\Model\Listing $listing, int $storeId): void
    {
        $listing->setStoreId($storeId);
        $this->listingRepository->save($listing);
    }

    private function addInstruction(int $listingId): void
    {
        $listingProductInstructionsData = [];

        foreach ($this->productRepository->findIdsByListingId($listingId) as $itemId) {
            $listingProductInstructionsData[] = [
                'listing_product_id' => $itemId,
                'type' => \M2E\Temu\Model\Listing::INSTRUCTION_TYPE_CHANGE_LISTING_STORE_VIEW,
                'initiator' => \M2E\Temu\Model\Listing::INSTRUCTION_INITIATOR_CHANGED_LISTING_STORE_VIEW,
                'priority' => 20,
            ];
        }

        $this->instructionService->createBatch($listingProductInstructionsData);
    }
}
