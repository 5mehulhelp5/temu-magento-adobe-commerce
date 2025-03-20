<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product;

class DataProvider
{
    use DataProviderTrait;

    /** @var \M2E\Temu\Model\Product\DataProvider\DataBuilderInterface[] */
    private array $dataBuilders = [];

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

    public function getVariantSkusForList(): DataProvider\Variants\Result
    {
        if ($this->hasResult(DataProvider\VariantsProvider::NICK)) {
            /** @var DataProvider\Variants\Result */
            return $this->getResult(DataProvider\VariantsProvider::NICK);
        }

        /** @var \M2E\Temu\Model\Product\DataProvider\VariantsProvider $builder */
        $builder = $this->getBuilder(\M2E\Temu\Model\Product\DataProvider\VariantsProvider::NICK);

        $value = $builder->getVariantSkusForList($this->product, $this->variantSettings);

        $result = DataProvider\Variants\Result::success($value, $builder->getWarningMessages());

        $this->addResult(DataProvider\VariantsProvider::NICK, $result);

        return $result;
    }

    public function getTitle(): DataProvider\Title\Result
    {
        if ($this->hasResult(DataProvider\TitleProvider::NICK)) {
            /** @var DataProvider\Title\Result */
            return $this->getResult(DataProvider\TitleProvider::NICK);
        }

        /** @var \M2E\Temu\Model\Product\DataProvider\TitleProvider $builder */
        $builder = $this->getBuilder(\M2E\Temu\Model\Product\DataProvider\TitleProvider::NICK);

        $title = $builder->getTitle($this->product);

        $result = DataProvider\Title\Result::success($title);

        $this->addResult(DataProvider\TitleProvider::NICK, $result);

        return $result;
    }

    public function getDescription(): DataProvider\Description\Result
    {
        if ($this->hasResult(DataProvider\DescriptionProvider::NICK)) {
            /** @var DataProvider\Description\Result */
            return $this->getResult(DataProvider\DescriptionProvider::NICK);
        }

        /** @var \M2E\Temu\Model\Product\DataProvider\DescriptionProvider $builder */
        $builder = $this->getBuilder(\M2E\Temu\Model\Product\DataProvider\DescriptionProvider::NICK);

        $value = $builder->getDescription($this->product);

        $result = DataProvider\Description\Result::success($value);

        $this->addResult(DataProvider\DescriptionProvider::NICK, $result);

        return $result;
    }

    public function getShipping(): DataProvider\Shipping\Result
    {
        if ($this->hasResult(DataProvider\ShippingProvider::NICK)) {
            /** @var DataProvider\Shipping\Result */
            return $this->getResult(DataProvider\ShippingProvider::NICK);
        }

        /** @var \M2E\Temu\Model\Product\DataProvider\ShippingProvider $builder */
        $builder = $this->getBuilder(\M2E\Temu\Model\Product\DataProvider\ShippingProvider::NICK);

        $value = $builder->getShippingData($this->product);

        $result = DataProvider\Shipping\Result::success($value, $builder->getWarningMessages());

        $this->addResult(DataProvider\ShippingProvider::NICK, $result);

        return $result;
    }

    public function getImages(): DataProvider\Images\Result
    {
        if ($this->hasResult(DataProvider\ImagesProvider::NICK)) {
            /** @var DataProvider\Images\Result */
            return $this->getResult(DataProvider\ImagesProvider::NICK);
        }

        /** @var \M2E\Temu\Model\Product\DataProvider\ImagesProvider $builder */
        $builder = $this->getBuilder(\M2E\Temu\Model\Product\DataProvider\ImagesProvider::NICK);

        $value = $builder->getImages($this->product);

        $result = DataProvider\Images\Result::success($value);

        $this->addResult(DataProvider\ImagesProvider::NICK, $result);

        return $result;
    }

    public function getCategoryData(): DataProvider\Category\Result
    {
        if ($this->hasResult(DataProvider\CategoryProvider::NICK)) {
            /** @var DataProvider\Category\Result */
            return $this->getResult(DataProvider\CategoryProvider::NICK);
        }

        /** @var \M2E\Temu\Model\Product\DataProvider\CategoryProvider $builder */
        $builder = $this->getBuilder(\M2E\Temu\Model\Product\DataProvider\CategoryProvider::NICK);
        $value = $builder->getCategoryData($this->product);
        $result = DataProvider\Category\Result::success($value);

        $this->addResult(DataProvider\CategoryProvider::NICK, $result);

        return $result;
    }

    public function getProductAttributesData(): DataProvider\Attributes\Result
    {
        if ($this->hasResult(DataProvider\ProductAttributesProvider::NICK)) {
            /** @var DataProvider\Attributes\Result */
            return $this->getResult(DataProvider\ProductAttributesProvider::NICK);
        }

        /** @var \M2E\Temu\Model\Product\DataProvider\ProductAttributesProvider $builder */
        $builder = $this->getBuilder(\M2E\Temu\Model\Product\DataProvider\ProductAttributesProvider::NICK);
        $value = $builder->getProductAttributesData($this->product);
        $result = DataProvider\Attributes\Result::success($value);

        $this->addResult(DataProvider\ProductAttributesProvider::NICK, $result);

        return $result;
    }

    public function getMetaData(): array
    {
        $metaData = [];
        foreach ($this->dataBuilders as $dataBuilder) {
            $metaData = array_merge($metaData, $dataBuilder->getMetaData());
        }

        return $metaData;
    }

    public function getVariantSkuIds(): array
    {
        /** @var \M2E\Temu\Model\Product\DataProvider\VariantsProvider $builder */
        $builder = $this->getBuilder(\M2E\Temu\Model\Product\DataProvider\VariantsProvider::NICK);

        return $builder->getVariantSkuIds($this->product, $this->variantSettings);
    }
}
