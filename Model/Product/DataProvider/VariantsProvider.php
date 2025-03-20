<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider;

class VariantsProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'Variants';

    private array $onlineDataForSku = [];

    private \M2E\Temu\Model\Settings $settings;

    public function __construct(
        \M2E\Temu\Model\Settings $settings
    ) {
        $this->settings = $settings;
    }

    public function getVariantSkus(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings
    ): array {
        $variants = $product->getVariants();
        $skuItems = new \M2E\Temu\Model\Product\DataProvider\Variants\Collection();

        foreach ($variants as $variant) {
            if ($variantSettings->isSkipAction($variant->getId())) {
                continue;
            }

            if ($variantSettings->isStopAction($variant->getId())) {
                $qty = 0;
            } else {
                $qty = $variant->getDataProvider()->getQty()->getValue();
                if ($variant->getDataProvider()->getQty()->getMessages()) {
                    $this->addWarningMessages(
                        $variant->getDataProvider()->getQty()->getMessages()
                    );
                }
            }

            $price = $variant->getDataProvider()->getPrice()->getValue()->price;
            if ($variant->getDataProvider()->getPrice()->getMessages()) {
                $this->addWarningMessages(
                    $variant->getDataProvider()->getQty()->getMessages()
                );
            }

            $currency = $variant->getProduct()->getCurrencyCode();

            $skuItems->addItem(
                $this->createVariantItem(
                    $variant,
                    $currency,
                    $price,
                    $qty
                )
            );
        }

        $this->onlineDataForSku = $skuItems->collectOnlineData();

        return $skuItems->toArray();
    }

    public function getVariantSkusForList(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings
    ): array {
        $variants = $product->getVariants();
        $skuItems = new \M2E\Temu\Model\Product\DataProvider\Variants\Collection();

        foreach ($variants as $variant) {
            if ($variantSettings->isSkipAction($variant->getId())) {
                continue;
            }

            $qty = $variant->getDataProvider()->getQty()->getValue();
            if ($variant->getDataProvider()->getQty()->getMessages()) {
                $this->addWarningMessages(
                    $variant->getDataProvider()->getQty()->getMessages()
                );
            }

            $price = $variant->getDataProvider()->getPrice()->getValue()->price;
            if ($variant->getDataProvider()->getPrice()->getMessages()) {
                $this->addWarningMessages(
                    $variant->getDataProvider()->getQty()->getMessages()
                );
            }

            $images = $variant->getDataProvider()->getImage()->getValue();

            $eanAttributeCode = $this->settings->getIdentifierCodeValue();
            $magentoProduct = $product->getMagentoProduct();
            $identifier = $magentoProduct->getAttributeValue($eanAttributeCode);

            $variationAttributes = $variant->getDataProvider()->getSalesAttributesData()->getValue();
            if ($variant->getDataProvider()->getSalesAttributesData()->getMessages()) {
                $this->addWarningMessages(
                    $variant->getDataProvider()->getSalesAttributesData()->getMessages()
                );
            }

            $packageWeight = $variant->getDataProvider()->getPackage()->getValue()->packageWeight;
            $packageDimensions = $variant->getDataProvider()->getPackage()->getValue()->packageDimensions;

            $skuItems->addItem(
                $this->createVariantItemForList(
                    $variant,
                    $identifier,
                    $price,
                    $qty,
                    [$images],
                    $variationAttributes,
                    $packageWeight,
                    $packageDimensions,
                )
            );
        }

        $this->onlineDataForSku = $skuItems->collectOnlineDataForList();

        return $skuItems->toArrayForList();
    }

    public function getMetaData(): array
    {
        return [self::NICK => $this->onlineDataForSku];
    }

    // ----------------------------------------

    public function getVariantSkuIds(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings
    ): array {
        $variants = $product->getVariants();

        $variantSkuIds = [];
        foreach ($variants as $variant) {
            if ($variantSettings->isSkipAction($variant->getId())) {
                continue;
            }

            $variantSkuIds[] = [
                'id' => $variant->getSkuId()
            ];
        }

        return $variantSkuIds;
    }

    // ----------------------------------------

    private function createVariantItem(
        \M2E\Temu\Model\Product\VariantSku $variant,
        string $currency,
        float $price,
        int $qty
    ): Variants\Item {
        $item = new Variants\Item();
        $item->setSkuId($variant->getSkuId());
        $item->setPrice($price);
        $item->setCurrency($currency);
        $item->setQty($qty);

        return $item;
    }

    private function createVariantItemForList(
        \M2E\Temu\Model\Product\VariantSku $variant,
        string $identifier,
        float $price,
        int $qty,
        array $images,
        array $variationAttributes,
        array $packageWeight,
        array $packageDimensions
    ): Variants\Item {
        $item = new Variants\Item();
        $item->setSku($variant->getSku());
        $item->setIdentifier($identifier);
        $item->setPrice($price);
        $item->setQty($qty);
        $item->setImages($images);
        $item->setVariationAttributes($variationAttributes);
        $item->setPackageWeight($packageWeight);
        $item->setPackageDimensions($packageDimensions);

        return $item;
    }

    private function addWarningMessages(array $messages): void
    {
        foreach ($messages as $message) {
            $this->addWarningMessage(
                $message
            );
        }
    }
}
