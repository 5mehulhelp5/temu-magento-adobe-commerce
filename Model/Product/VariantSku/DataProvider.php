<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku;

use M2E\Temu\Model\Product\DataProvider\AbstractResult;
use M2E\Temu\Model\Product\DataProviderTrait;

class DataProvider
{
    use DataProviderTrait;

    private \M2E\Temu\Model\Product\VariantSku\DataProvider\Factory $dataBuilderFactory;
    private \M2E\Temu\Model\Product\VariantSku $variantSku;
    private \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings;

    public function __construct(
        \M2E\Temu\Model\Product\VariantSku $variantSku,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings,
        \M2E\Temu\Model\Product\VariantSku\DataProvider\Factory $dataBuilderFactory
    ) {
        $this->variantSku = $variantSku;
        $this->variantSettings = $variantSettings;
        $this->dataBuilderFactory = $dataBuilderFactory;
    }

    // ----------------------------------------

    public function getPrice(): DataProvider\Price\Result
    {
        if ($this->hasResult(DataProvider\PriceProvider::NICK)) {
            /** @var \M2E\Temu\Model\Product\VariantSku\DataProvider\Price\Result */
            return $this->getResult(DataProvider\PriceProvider::NICK);
        }

        /** @var \M2E\Temu\Model\Product\VariantSku\DataProvider\PriceProvider $builder */
        $builder = $this->getBuilder(DataProvider\PriceProvider::NICK);

        $value = $builder->getPrice($this->variantSku);

        $result = DataProvider\Price\Result::success($value);

        $this->addResult(DataProvider\PriceProvider::NICK, $result);

        return $result;
    }

    public function getQty(): DataProvider\Qty\Result
    {
        if ($this->hasResult(DataProvider\QtyProvider::NICK)) {
            /** @var \M2E\Temu\Model\Product\VariantSku\DataProvider\Qty\Result */
            return $this->getResult(DataProvider\QtyProvider::NICK);
        }

        /** @var \M2E\Temu\Model\Product\VariantSku\DataProvider\QtyProvider $builder */
        $builder = $this->getBuilder(DataProvider\QtyProvider::NICK);

        $value = $builder->getQty($this->variantSku);

        $result = DataProvider\Qty\Result::success($value, $builder->getWarningMessages());

        $this->addResult(DataProvider\QtyProvider::NICK, $result);

        return $result;
    }

    private function getBuilder(
        string $nick
    ): \M2E\Temu\Model\Product\DataProvider\DataBuilderInterface {
        if (isset($this->dataBuilders[$nick])) {
            return $this->dataBuilders[$nick];
        }

        return $this->dataBuilders[$nick] = $this->dataBuilderFactory->create($nick);
    }
}
