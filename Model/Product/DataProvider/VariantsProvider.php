<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider;

class VariantsProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'Variants';

    private array $onlineDataForSku = [];

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

    private function addWarningMessages(array $messages): void
    {
        foreach ($messages as $message) {
            $this->addWarningMessage(
                $message
            );
        }
    }
}
