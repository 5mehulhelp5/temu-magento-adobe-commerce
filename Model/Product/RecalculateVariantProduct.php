<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product;

class RecalculateVariantProduct
{
    private const INSTRUCTION_INITIATOR_UNASSIGN_VARIANT_FROM_MAGENTO = 'unassign_variant_from_magento';
    private const INSTRUCTION_INITIATOR_ASSIGN_VARIANT_FROM_MAGENTO = 'assign_variant_from_magento';

    private \M2E\Temu\Model\InstructionService $instructionService;
    private \M2E\Temu\Model\Product\Repository $productRepository;
    private \M2E\Temu\Model\Listing\LogService $listingLogService;
    private \M2E\Temu\Model\Product\VariantSkuFactory $variantSkuFactory;
    private \M2E\Temu\Model\Listing\Repository $listingRepository;
    private \M2E\Temu\Model\Product\LockManager $productLockManager;

    private int $actionId;

    public function __construct(
        \M2E\Temu\Model\InstructionService $instructionService,
        \M2E\Temu\Model\Product\Repository $productRepository,
        \M2E\Temu\Model\Listing\LogService $listingLogService,
        \M2E\Temu\Model\Product\VariantSkuFactory $variantSkuFactory,
        \M2E\Temu\Model\Listing\Repository $listingRepository,
        \M2E\Temu\Model\Product\LockManager $productLockManager
    ) {
        $this->instructionService = $instructionService;
        $this->productRepository = $productRepository;
        $this->listingLogService = $listingLogService;
        $this->variantSkuFactory = $variantSkuFactory;
        $this->listingRepository = $listingRepository;
        $this->productLockManager = $productLockManager;
    }

    /**
     * @param \Magento\Catalog\Model\Product $magentoProduct
     * @param \M2E\Temu\Model\Product\AffectedProduct\Product[] $affectedProducts
     *
     * @return void
     */
    public function process(
        \Magento\Catalog\Model\Product $magentoProduct,
        array $affectedProducts
    ): void {
        if (empty($magentoProduct->getTypeInstance()->getUsedProducts($magentoProduct))) {
            return;
        }

        $this->actionId = $this->listingLogService->getNextActionId();
        $magentoVariations = [];
        foreach ($magentoProduct->getTypeInstance()->getUsedProducts($magentoProduct) as $variation) {
            $magentoVariations[$variation->getEntityId()] = $variation;
        }

        foreach ($affectedProducts as $affectedProduct) {
            $temuProduct = $affectedProduct->getProduct();
            if (empty($temuProduct->getVariants())) {
                continue;
            }

            $temuProductVariants = $this->processTemuProductVariants($temuProduct, $magentoVariations);
            //$this->processMagentoProductVariants($temuProduct, $magentoVariations, $temuProductVariants);
        }
    }

    /**
     * @param \M2E\Temu\Model\Product $temuProduct
     * @param array $magentoVariations
     * @param array $temuProductVariants
     *
     * @return void
     */
    private function processMagentoProductVariants(
        \M2E\Temu\Model\Product $temuProduct,
        array $magentoVariations,
        array $temuProductVariants
    ): void {
        foreach ($magentoVariations as $magentoVariation) {
            $magentoVariationId = (int)$magentoVariation->getId();
            if (!isset($temuProductVariants[$magentoVariationId])) {
                $this->assignVariant($temuProduct, $magentoVariationId);
            }
        }
    }

    /**
     * @param \M2E\Temu\Model\Product $temuProduct
     * @param array<integer, \Magento\Catalog\Model\Product> $magentoVariations
     *
     * @return array<integer, \M2E\Temu\Model\Product\VariantSku>
     */
    private function processTemuProductVariants(
        \M2E\Temu\Model\Product $temuProduct,
        array $magentoVariations
    ): array {
        $temuProductVariants = [];
        foreach ($temuProduct->getVariants() as $ttsVariant) {
            $ttsProductVariants[$ttsVariant->getMagentoProductId()] = $ttsVariant;
            if (!isset($magentoVariations[$ttsVariant->getMagentoProductId()])) {
                $this->unassignVariant($temuProduct, $ttsVariant);
            }
        }
        return $temuProductVariants;
    }

    private function unassignVariant(
        \M2E\Temu\Model\Product $temuParentProduct,
        \M2E\Temu\Model\Product\VariantSku $temuVariant
    ): void {
        if (!$temuVariant->isStatusNotListed()) {
            return;
        }

        $this->productRepository->deleteVariantSku($temuVariant);

        if ($temuParentProduct->isStatusListed()) {
            $this->instructionService->create(
                $temuParentProduct->getId(),
                \M2E\Temu\Model\Product::INSTRUCTION_TYPE_VARIANT_SKU_REMOVED,
                self::INSTRUCTION_INITIATOR_UNASSIGN_VARIANT_FROM_MAGENTO,
                80,
            );
        }

        $this->addUnassignVariantLog($temuVariant);
    }

    private function assignVariant(\M2E\Temu\Model\Product $temuParentProduct, int $magentoProductId): void
    {
        $listing = $this->listingRepository->get($temuParentProduct->getListingId());

        if (!$listing->getShop()->hasDefaultWarehouse()) {
            return;
        }

        $warehouseId = $listing->getShop()->getDefaultWarehouse()->getId();

        $variantSku = $this->variantSkuFactory->create();

        $variantSku->init($temuParentProduct, $magentoProductId);
        $this->productRepository->saveVariantSku($variantSku);

        if ($temuParentProduct->isStatusListed()) {
            $this->instructionService->create(
                $temuParentProduct->getId(),
                \M2E\Temu\Model\Product::INSTRUCTION_TYPE_VARIANT_SKU_ADDED,
                self::INSTRUCTION_INITIATOR_ASSIGN_VARIANT_FROM_MAGENTO,
                80,
            );
        }

        $this->addAssignVariantLog($variantSku);
    }

    private function addAssignVariantLog(\M2E\Temu\Model\Product\VariantSku $variantSku): void
    {
        $message = (string)__(
            'SKU %sku: The variation was added to the product',
            ['sku' => $variantSku->getSku()]
        );

        $this->addLog(
            $variantSku,
            $message,
            \M2E\Temu\Model\Listing\Log::ACTION_ADD_PRODUCT_TO_LISTING
        );
    }

    private function addUnassignVariantLog(\M2E\Temu\Model\Product\VariantSku $variantSku): void
    {
        $message = (string)__(
            'SKU %sku: The variation was removed from the product.',
            ['sku' => $variantSku->getSku()]
        );

        $this->addLog(
            $variantSku,
            $message,
            \M2E\Temu\Model\Listing\Log::ACTION_DELETE_PRODUCT_FROM_LISTING
        );
    }

    private function addLog(
        \M2E\Temu\Model\Product\VariantSku $variantSku,
        string $message,
        int $action
    ): void {
        $this->listingLogService->addProduct(
            $variantSku->getProduct(),
            \M2E\Core\Helper\Data::INITIATOR_EXTENSION,
            $action,
            $this->actionId,
            $message,
            \M2E\Temu\Model\Log\AbstractModel::TYPE_WARNING,
        );
    }
}
