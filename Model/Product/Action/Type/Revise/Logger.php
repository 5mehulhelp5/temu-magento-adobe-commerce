<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\Revise;

class Logger
{
    private array $logs = [];
    private \Magento\Framework\Locale\CurrencyInterface $localeCurrency;

    private float $onlinePrice;
    private int $onlineQty;

    public function __construct(
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency
    ) {
        $this->localeCurrency = $localeCurrency;
    }

    public function saveVariantOnlineDataBeforeUpdate(\M2E\Temu\Model\Product\VariantSku\OnlineData $variantOnlineData): void
    {
        $this->onlinePrice = $variantOnlineData->getPrice();
        $this->onlineQty = $variantOnlineData->getQty();
    }

    public function collectSuccessMessages(\M2E\Temu\Model\Product\VariantSku $variant): array
    {
        $this->generateMessageAboutChangePrice($variant);
        $this->generateMessageAboutChangeQty($variant);

        return $this->logs;
    }

    private function generateMessageAboutChangePrice(\M2E\Temu\Model\Product\VariantSku $variant): void
    {
        $from = $this->onlinePrice;
        $currencyCode =  $variant->getProduct()->getCurrencyCode();
        $currency = $this->localeCurrency->getCurrency($currencyCode);

        if ($from === $variant->getOnlinePrice()) {
            return;
        }

        if ($variant->getProduct()->isSimple()) {
            $message = sprintf(
                'Product Price was revised from %s to %s',
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

        $this->logs[] = $message;
    }

    private function generateMessageAboutChangeQty(\M2E\Temu\Model\Product\VariantSku $variant): void
    {
        $from = $this->onlineQty;
        if ($from === $variant->getOnlineQty()) {
            return;
        }

        if ($variant->getProduct()->isSimple()) {
            $message = sprintf(
                'Product QTY was revised from %s to %s',
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

        $this->logs[] = $message;
    }
}
