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

    public function __construct(
        \M2E\Temu\Model\Product\Repository $productRepository,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \M2E\Temu\Model\Tag\ListingProduct\Buffer $tagBuffer,
        \M2E\Temu\Model\TagFactory $tagFactory
    ) {
        parent::__construct($tagBuffer, $tagFactory);

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

            $variantSkuId = $variant->getSkuId();

            if (!isset($metadata[DataProvider\VariantsProvider::NICK][$variantSkuId])) {
                continue;
            }

            if ($this->isSuccessPrice($variantSkuId)) {
                $this->processSuccessRevisePrice($beforeData[$variant->getId()], $variant);
            }

            if ($this->isSuccessQty($variantSkuId)) {
                $this->processSuccessReviseQty($beforeData[$variant->getId()], $variant);
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
        \M2E\Temu\Model\Product\VariantSku\OnlineData $beforeVariantOnlineData,
        \M2E\Temu\Model\Product\VariantSku $variant
    ): void {
        $variant->setOnlinePrice($this->getOnlinePriceForVariant($variant->getSkuId()));
        $variant->setPriceActualizeDate(
            \M2E\Core\Helper\Date::createDateGmt($this->priceUpdateBySkuId['request_time'])
        );

        $this->productRepository->saveVariantSku($variant);

        $from = $beforeVariantOnlineData->getPrice();
        $currencyCode =  $this->getProduct()->getCurrencyCode();
        $currency = $this->localeCurrency->getCurrency($currencyCode);

        if ($from === $variant->getOnlinePrice()) {
            return;
        }

        if ($this->getProduct()->isSimple()) {
            $message = sprintf(
                'Price was revised from %s to %s',
                $currency->toCurrency($from),
                $currency->toCurrency($variant->getOnlinePrice()),
            );
        } else {
            $message = sprintf(
                'SKU %s: Price was revised from %s to %s',
                $variant->getSkuId(),
                $currency->toCurrency($from),
                $currency->toCurrency($variant->getOnlinePrice()),
            );
        }

        $this->getLogBuffer()->addSuccess($message);
    }

    private function processSuccessReviseQty(
        \M2E\Temu\Model\Product\VariantSku\OnlineData $beforeVariantOnlineData,
        \M2E\Temu\Model\Product\VariantSku $variant
    ): void {
        $variant->setOnlineQty($this->getOnlineQtyForVariant($variant->getSkuId()));
        $variant->setQtyActualizeDate(
            \M2E\Core\Helper\Date::createDateGmt($this->qtyUpdateBySkuId['request_time'])
        );
        $this->productRepository->saveVariantSku($variant);

        $from = $beforeVariantOnlineData->getQty();
        if ($from === $variant->getOnlineQty()) {
            return;
        }

        if ($this->getProduct()->isSimple()) {
            $message = sprintf(
                'QTY was revised from %s to %s',
                $from,
                $variant->getOnlineQty()
            );
        } else {
            $message = sprintf(
                'SKU %s: QTY was revised from %s to %s',
                $variant->getSkuId(),
                $from,
                $variant->getOnlineQty()
            );
        }

        $this->getLogBuffer()->addSuccess($message);
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
