<?php

namespace M2E\Temu\Plugin\MSI\Magento\Inventory\Model\SourceItem\Command;

class Delete extends \M2E\Temu\Plugin\AbstractPlugin
{
    private \M2E\Temu\Model\MSI\AffectedProducts $msiAffectedProducts;
    private \M2E\Temu\Model\Magento\Product\ChangeAttributeTrackerFactory $changeAttributeTrackerFactory;
    private \M2E\Temu\Model\Listing\LogService $listingLogService;

    public function __construct(
        \M2E\Temu\Model\Listing\LogService $listingLogService,
        \M2E\Temu\Model\MSI\AffectedProducts $msiAffectedProducts,
        \M2E\Temu\Model\Magento\Product\ChangeAttributeTrackerFactory $changeAttributeTrackerFactory
    ) {
        $this->msiAffectedProducts = $msiAffectedProducts;
        $this->changeAttributeTrackerFactory = $changeAttributeTrackerFactory;
        $this->listingLogService = $listingLogService;
    }

    public function aroundExecute($interceptor, \Closure $callback, ...$arguments)
    {
        return $this->execute('execute', $interceptor, $callback, $arguments);
    }

    protected function processExecute($interceptor, \Closure $callback, array $arguments)
    {
        /** @var \Magento\InventoryApi\Api\Data\SourceItemInterface[] $sourceItems */
        $sourceItems = $arguments[0];

        $result = $callback(...$arguments);

        foreach ($sourceItems as $sourceItem) {
            $affectedProductCollection = $this->msiAffectedProducts->getAffectedProductsBySourceAndSku(
                $sourceItem->getSourceCode(),
                $sourceItem->getSku()
            );

            if ($affectedProductCollection->isEmpty()) {
                continue;
            }

            $this->addListingProductInstructions($affectedProductCollection);

            foreach ($affectedProductCollection->getProducts() as $product) {
                $this->logListingProductMessage($product, $sourceItem);
            }
        }

        return $result;
    }

    private function logListingProductMessage(
        \M2E\Temu\Model\Product\AffectedProduct\Product $affectedProduct,
        \Magento\InventoryApi\Api\Data\SourceItemInterface $sourceItem
    ): void {
        $this->listingLogService->addProduct(
            $affectedProduct->getProduct(),
            \M2E\Core\Helper\Data::INITIATOR_EXTENSION,
            \M2E\Temu\Model\Listing\Log::ACTION_CHANGE_PRODUCT_QTY,
            null,
            \M2E\Temu\Helper\Module\Log::encodeDescription(
                'The "%source%" Source was unassigned from product.',
                ['!source' => $sourceItem->getSourceCode()]
            ),
            \M2E\Temu\Model\Log\AbstractModel::TYPE_INFO
        );
    }

    /**
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    private function addListingProductInstructions(
        \M2E\Temu\Model\Product\AffectedProduct\Collection $affectedProductCollection
    ): void {
        foreach ($affectedProductCollection->getProducts() as $affectedProduct) {
            $changeAttributeTracker = $this->changeAttributeTrackerFactory->create(
                $affectedProduct->getProduct()
            );
            $changeAttributeTracker->addInstructionWithPotentiallyChangedType();
            $changeAttributeTracker->flushInstructions();
        }
    }
}
