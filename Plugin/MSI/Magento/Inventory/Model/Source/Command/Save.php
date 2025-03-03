<?php

namespace M2E\Temu\Plugin\MSI\Magento\Inventory\Model\Source\Command;

class Save extends \M2E\Temu\Plugin\AbstractPlugin
{
    /** @var \M2E\Temu\Model\MSI\AffectedProducts */
    private $msiAffectedProducts;

    /** @var \M2E\Temu\PublicServices\Product\SqlChange */
    private $publicService;

    // ---------------------------------------

    /** @var \Magento\Inventory\Model\SourceRepository */
    protected $sourceRepo;

    private \M2E\Temu\Model\Listing\LogService $listingLogService;

    private \M2E\Temu\Model\Product\Repository $productRepository;

    /*
    * Dependencies can not be specified in constructor because MSI modules can be not installed.
    */
    public function __construct(
        \M2E\Temu\Model\Product\Repository $productRepository,
        \M2E\Temu\Model\Listing\LogService $listingLogService,
        \M2E\Temu\Model\MSI\AffectedProducts $msiAffectedProducts,
        \M2E\Temu\PublicServices\Product\SqlChange $publicService,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->sourceRepo = $objectManager->get(\Magento\Inventory\Model\SourceRepository::class);
        $this->msiAffectedProducts = $msiAffectedProducts;
        $this->publicService = $publicService;
        $this->listingLogService = $listingLogService;
        $this->productRepository = $productRepository;
    }

    //########################################

    /**
     * @param $interceptor
     * @param \Closure $callback
     * @param array ...$arguments
     *
     * @return mixed
     */
    public function aroundExecute($interceptor, \Closure $callback, ...$arguments)
    {
        return $this->execute('execute', $interceptor, $callback, $arguments);
    }

    /**
     * @param $interceptor
     * @param \Closure $callback
     * @param array $arguments
     *
     * @return mixed
     */
    protected function processExecute($interceptor, \Closure $callback, array $arguments)
    {
        /** @var \Magento\InventoryApi\Api\Data\SourceInterface $source */
        $source = $arguments[0];

        try {
            $sourceBefore = $this->sourceRepo->get($source->getSourceCode());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $noSuchEntityException) {
            return $callback(...$arguments);
        }

        $result = $callback(...$arguments);

        if ($sourceBefore->isEnabled() === $source->isEnabled()) {
            return $result;
        }

        $oldValue = $sourceBefore->isEnabled() ? 'Enabled' : 'Disabled';
        $newValue = $source->isEnabled() ? 'Enabled' : 'Disabled';

        foreach ($this->msiAffectedProducts->getAffectedListingsBySource($source->getSourceCode()) as $listing) {
            foreach ($this->productRepository->findMagentoProductIdsByListingId($listing->getId()) as $productId) {
                $this->publicService->markQtyWasChanged($productId);
            }
            $this->logListingMessage($listing, $source, $oldValue, $newValue);
        }
        $this->publicService->applyChanges();

        return $result;
    }

    //########################################

    private function logListingMessage(
        \M2E\Temu\Model\Listing $listing,
        \Magento\InventoryApi\Api\Data\SourceInterface $source,
        $oldValue,
        $newValue
    ) {
        $this->listingLogService->addListing(
            $listing,
            \M2E\Core\Helper\Data::INITIATOR_EXTENSION,
            \M2E\Temu\Model\Listing\Log::ACTION_UNKNOWN,
            null,
            \M2E\Temu\Helper\Module\Log::encodeDescription(
                'Status of the "%source%" Source changed [%from%] to [%to%].',
                ['!from' => $oldValue, '!to' => $newValue, '!source' => $source->getSourceCode()]
            ),
            \M2E\Temu\Model\Log\AbstractModel::TYPE_INFO,
        );
    }
}
