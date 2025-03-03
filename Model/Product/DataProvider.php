<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product;

class DataProvider
{
    use DataProviderTrait;

    /** @var \M2E\Temu\Model\Product\DataProvider\Factory */
    private \M2E\Temu\Model\Product\DataProvider\Factory $dataBuilderFactory;

    private \M2E\Temu\Model\Product $product;
    private \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings;

    public function __construct(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings,
        \M2E\Temu\Model\Product\DataProvider\Factory $dataBuilderFactory
    ) {
        $this->product = $product;
        $this->variantSettings = $variantSettings;
        $this->dataBuilderFactory = $dataBuilderFactory;
    }

    public function getVariantSkus(): DataProvider\Variants\Result
    {
        if ($this->hasResult(DataProvider\VariantsProvider::NICK)) {
            /** @var DataProvider\Variants\Result */
            return $this->getResult(DataProvider\VariantsProvider::NICK);
        }

        /** @var \M2E\Temu\Model\Product\DataProvider\VariantsProvider $builder */
        $builder = $this->getBuilder(\M2E\Temu\Model\Product\DataProvider\VariantsProvider::NICK);

        $value = $builder->getVariantSkus($this->product, $this->variantSettings);

        $result = DataProvider\Variants\Result::success($value, $builder->getWarningMessages());

        $this->addResult(DataProvider\VariantsProvider::NICK, $result);

        return $result;
    }

    public function getMetaData(): array
    {
        /** @var \M2E\Temu\Model\Product\DataProvider\VariantsProvider $builder */
        $builder = $this->getBuilder(\M2E\Temu\Model\Product\DataProvider\VariantsProvider::NICK);

        return $builder->getMetaData();
    }

    public function getVariantSkuIds(): array
    {
        /** @var \M2E\Temu\Model\Product\DataProvider\VariantsProvider $builder */
        $builder = $this->getBuilder(\M2E\Temu\Model\Product\DataProvider\VariantsProvider::NICK);

        return $builder->getVariantSkuIds($this->product, $this->variantSettings);
    }
}
