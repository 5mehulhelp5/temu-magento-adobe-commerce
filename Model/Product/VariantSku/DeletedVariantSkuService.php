<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku;

class DeletedVariantSkuService
{
    /** @var \M2E\Temu\Model\Product\VariantSku\DeletedFactory */
    private DeletedFactory $variantSkuDeletedFactory;
    /** @var \M2E\Temu\Model\Product\VariantSku\Deleted\Repository */
    private Deleted\Repository $variantSkuDeletedRepository;

    public function __construct(
        DeletedFactory $variantSkuDeletedFactory,
        Deleted\Repository $variantSkuDeletedRepository
    ) {
        $this->variantSkuDeletedFactory = $variantSkuDeletedFactory;
        $this->variantSkuDeletedRepository = $variantSkuDeletedRepository;
    }

    public function backupVariantSku(\M2E\Temu\Model\Product\VariantSku $variantSku): void
    {
        $variantSkuDeleted = $this->variantSkuDeletedFactory->create();
        $variantSkuDeleted = $variantSkuDeleted->initFromVariant($variantSku);

        $this->variantSkuDeletedRepository->create($variantSkuDeleted);
    }

    public function deleteForProduct(\M2E\Temu\Model\Product $product): void
    {
        $deletedVariations = $this->variantSkuDeletedRepository->getByProductId($product->getId());
        foreach ($deletedVariations as $deletedVariation) {
            $this->variantSkuDeletedRepository->delete($deletedVariation);
        }
    }
}
