<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\Revise;

use M2E\Temu\Model\Product\DataProvider;

class Response extends \M2E\Temu\Model\Product\Action\Type\AbstractResponse
{
    private \M2E\Temu\Model\Product\Repository $productRepository;
    private $priceUpdateBySkuId = null;
    private $qtyUpdateBySkuId = null;
    protected \Magento\Framework\Locale\CurrencyInterface $localeCurrency;
    private \M2E\Temu\Model\Product\Action\Type\Revise\LoggerFactory $loggerFactory;

    public function __construct(
        \M2E\Temu\Model\Product\Repository $productRepository,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \M2E\Temu\Model\Tag\ListingProduct\Buffer $tagBuffer,
        \M2E\Temu\Model\TagFactory $tagFactory,
        \M2E\Temu\Model\Product\Action\Type\Revise\LoggerFactory $loggerFactory
    ) {
        parent::__construct($tagBuffer, $tagFactory);

        $this->loggerFactory = $loggerFactory;
        $this->productRepository = $productRepository;
        $this->localeCurrency = $localeCurrency;
    }

    public function process(): void
    {
        $responseData = $this->getResponseData();
        if (!empty($responseData['messages'])) {
            $this->addTags($responseData['messages']);
        }

        if ($this->isSuccess()) {
            $this->processSuccess();
        }
    }

    public function generateResultMessage(): void
    {
        $responseData = $this->getResponseData();

        foreach ($responseData['messages'] ?? [] as $messageData) {
            if ($messageData['type'] === \M2E\Core\Model\Response\Message::TYPE_ERROR) {
                $this->getLogBuffer()->addFail($messageData['text']);
            }

            if ($messageData['type'] === \M2E\Core\Model\Response\Message::TYPE_WARNING) {
                $this->getLogBuffer()->addWarning($messageData['text']);
            }
        }
    }

    protected function processSuccess(): void
    {
        $product = $this->getProduct();

        $metadata = $this->getRequestMetaData();
        if (isset($metadata[DataProvider\VariantsProvider::NICK])) {
            $this->updateVariants($product->getVariants(), $metadata);
        }

        $this->updateProduct($product, $metadata);
    }

    private function isSuccess(): bool
    {
        $responseData = $this->getResponseData();

        return $responseData['status'] === true;
    }

    /**
     * @param \M2E\Temu\Model\Product\VariantSku[] $variants
     * @param array $metadata
     *
     * @return void
     */
    private function updateVariants(array $variants, array $metadata): void
    {
        $beforeData = [];
        foreach ($this->getProduct()->getVariantOnlineData() as $onlineData) {
            $beforeData[$onlineData->getVariantId()] = $onlineData;
        }

        foreach ($variants as $variant) {
            if ($this->getVariantSettings()->isSkipAction($variant->getId())) {
                continue;
            }

            $logger = $this->loggerFactory->create();
            $logger->saveVariantOnlineDataBeforeUpdate($beforeData[$variant->getId()]);

            $variantSkuId = $variant->getSkuId();

            if (!isset($metadata[DataProvider\VariantsProvider::NICK][$variantSkuId])) {
                continue;
            }

            if ($this->isSuccessPrice($variantSkuId)) {
                $this->processSuccessRevisePrice($variant);
            }

            if ($this->isSuccessQty($variantSkuId)) {
                $this->processSuccessReviseQty($variant);
            }

            $messages = $logger->collectSuccessMessages($variant);
            if (empty($messages)) {
                $this->getLogBuffer()->addSuccess('Item was revised');
            }

            foreach ($messages as $message) {
                $this->getLogBuffer()->addSuccess($message);
            }
        }
    }

    private function isSuccessPrice($skuId): bool
    {
        $responseData = $this->getResponseData();
        $priceUpdateByVariant = $responseData['data']['price'];

        if (empty($priceUpdateByVariant)) {
            return false;
        }

        if ($this->priceUpdateBySkuId === null) {
            $this->preparePriceUpdateBySkuId($priceUpdateByVariant);
        }

        return $this->priceUpdateBySkuId[$skuId] ?? false;
    }

    private function isSuccessQty($skuId): bool
    {
        $responseData = $this->getResponseData();
        $qtyUpdateByVariant = $responseData['data']['qty'];

        if (empty($qtyUpdateByVariant)) {
            return false;
        }

        if ($this->qtyUpdateBySkuId === null) {
            $this->prepareQtyUpdateBySkuId($qtyUpdateByVariant);
        }

        return $this->qtyUpdateBySkuId[$skuId] ?? false;
    }

    private function processSuccessRevisePrice(
        \M2E\Temu\Model\Product\VariantSku $variant
    ): void {
        $variant->setOnlinePrice($this->getOnlinePriceForVariant($variant->getSkuId()));
        $variant->setPriceActualizeDate(
            \M2E\Core\Helper\Date::createDateGmt($this->priceUpdateBySkuId['request_time'])
        );

        $this->productRepository->saveVariantSku($variant);
    }

    private function processSuccessReviseQty(
        \M2E\Temu\Model\Product\VariantSku $variant
    ): void {
        $variant->setOnlineQty($this->getOnlineQtyForVariant($variant->getSkuId()));
        $variant->setQtyActualizeDate(
            \M2E\Core\Helper\Date::createDateGmt($this->qtyUpdateBySkuId['request_time'])
        );
        $this->productRepository->saveVariantSku($variant);
    }

    private function getOnlinePriceForVariant(string $skuId): float
    {
        $metadata = $this->getRequestMetaData();

        return $metadata[DataProvider\VariantsProvider::NICK][$skuId]['online_price'] ?? 0;
    }

    private function getOnlineQtyForVariant(string $skuId): int
    {
        $metadata = $this->getRequestMetaData();

        return $metadata[DataProvider\VariantsProvider::NICK][$skuId]['online_qty'] ?? 0;
    }

    private function updateProduct(
        \M2E\Temu\Model\Product $product,
        array $metadata
    ): void {
        if (isset($metadata[DataProvider\VariantsProvider::NICK])) {
            $product->setOnlineQty($this->getOnlineQtyFromVariants($metadata));
        }

        $product
            ->recalculateOnlineDataByVariants()
            ->removeBlockingByError();

        $this->productRepository->save($product);
    }

    private function getOnlineQtyFromVariants(array $metadata): int
    {
        $qty = 0;
        foreach ($metadata[DataProvider\VariantsProvider::NICK] as $variantData) {
            $qty += $variantData['online_qty'] ?? 0;
        }

        return $qty;
    }

    private function preparePriceUpdateBySkuId(array $priceData): void
    {
        $this->priceUpdateBySkuId = [];

        foreach ($priceData['skus'] as $variantData) {
            $this->priceUpdateBySkuId[$variantData['id']] = $variantData['status'];
        }

        $this->priceUpdateBySkuId['request_time'] = $priceData['request_time'];
    }

    private function prepareQtyUpdateBySkuId(array $qtyData): void
    {
        $this->qtyUpdateBySkuId = [];

        foreach ($qtyData['skus'] as $variantData) {
            $this->qtyUpdateBySkuId[$variantData['id']] = $variantData['status'];
        }

        $this->qtyUpdateBySkuId['request_time'] = $qtyData['request_time'];
    }
}
