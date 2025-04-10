<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\DataProvider\Attributes;

class Processor
{
    /** @var \M2E\Temu\Model\Category\Dictionary\Attribute\SalesAttribute[] */
    private array $attributes;
    private \M2E\Temu\Model\Category\Attribute\Repository $attributeRepository;
    private \M2E\Temu\Helper\Module\Renderer\Description $descriptionRender;
    private \M2E\Temu\Model\Product\DataProvider\Attributes\NotFoundAttributeDetector $notFoundAttributeDetector;
    private \M2E\Temu\Model\Category\Attribute\Recommended\RetrieveValue $recommendedProcessor;

    public function __construct(
        \M2E\Temu\Model\Category\Attribute\Repository $attributeRepository,
        \M2E\Temu\Helper\Module\Renderer\Description $descriptionRender,
        \M2E\Temu\Model\Product\DataProvider\Attributes\NotFoundAttributeDetector $notFoundAttributeDetector,
        \M2E\Temu\Model\Category\Attribute\Recommended\RetrieveValue $recommendedProcessor
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->descriptionRender = $descriptionRender;
        $this->notFoundAttributeDetector = $notFoundAttributeDetector;
        $this->recommendedProcessor = $recommendedProcessor;
    }

    /**
     * @param \M2E\Temu\Model\Product\VariantSku $listingProduct
     *
     * @return \M2E\Temu\Model\Product\VariantSku\DataProvider\Attributes\Item[]
     */
    public function getAttributes(
        \M2E\Temu\Model\Product\VariantSku $listingProduct
    ): array {
        $result = [];
        $this->initDictionarySalesAttributes($listingProduct);
        $attributes = $this->getDictionaryCategoryAttributes($listingProduct->getProduct()->getTemplateCategoryId());
        $magentoProduct = $listingProduct->getMagentoProduct();

        $this->notFoundAttributeDetector->clearMessages();
        $this->notFoundAttributeDetector->searchNotFoundAttributes($magentoProduct);

        foreach ($attributes as $attribute) {
            $this->processAttribute($attribute, $magentoProduct, $result);
        }

        $this->notFoundAttributeDetector->processNotFoundAttributes(
            $magentoProduct,
            $listingProduct->getListing()->getStoreId(),
            (string)__('Variants')
        );

        return $result;
    }

    public function getWarningMessages(): array
    {
        return $this->notFoundAttributeDetector->getWarningMessages();
    }

    private function processAttribute(
        \M2E\Temu\Model\Category\CategoryAttribute $attribute,
        \M2E\Temu\Model\Magento\Product $magentoProduct,
        array &$result
    ): void {
        $dictionaryAttribute = $this->getDictionaryAttributeById($attribute->getAttributeId());
        if ($attribute->isValueModeNone() || !$dictionaryAttribute) {
            return;
        }

        $recommendedValue = $this->recommendedProcessor->retrieveValue(
            $attribute,
            $dictionaryAttribute,
            $magentoProduct
        );

        if ($recommendedValue) {
            $this->handleRecommendedValue($recommendedValue, $attribute, $result);
            return;
        }

        switch ($attribute->getValueMode()) {
            case \M2E\Temu\Model\Category\CategoryAttribute::VALUE_MODE_RECOMMENDED:
                $this->handleRecommendedMode($attribute, $result);
                break;
            case \M2E\Temu\Model\Category\CategoryAttribute::VALUE_MODE_CUSTOM_VALUE:
                $this->handleCustomValueMode($attribute, $magentoProduct, $result);
                break;
            case \M2E\Temu\Model\Category\CategoryAttribute::VALUE_MODE_CUSTOM_ATTRIBUTE:
                $this->handleCustomAttributeMode($attribute, $magentoProduct, $result);
                break;
        }
    }

    private function initDictionarySalesAttributes(\M2E\Temu\Model\Product\VariantSku $listingProduct): void
    {
        $dictionary = $listingProduct->getCategoryDictionary();
        foreach ($dictionary->getSalesAttributes() as $attribute) {
            $this->attributes[$attribute->getId()] = $attribute;
        }
    }

    /**
     * @param int $categoryId
     *
     * @return \M2E\Temu\Model\Category\CategoryAttribute[]
     */
    private function getDictionaryCategoryAttributes(int $categoryId): array
    {
        return $this->attributeRepository->findByDictionaryId(
            $categoryId,
            [\M2E\Temu\Model\Category\CategoryAttribute::ATTRIBUTE_TYPE_SALES]
        );
    }

    private function getDictionaryAttributeById(
        string $attributeId
    ): ?\M2E\Temu\Model\Category\Dictionary\Attribute\SalesAttribute {
        return $this->attributes[$attributeId] ?? null;
    }

    private function handleRecommendedValue(
        \M2E\Temu\Model\Category\Attribute\Recommended\Result $recommendedValue,
        \M2E\Temu\Model\Category\CategoryAttribute $attribute,
        array &$result
    ): void {
        if ($recommendedValue->isFail()) {
            $this->notFoundAttributeDetector->addWarningMessage($recommendedValue->getFailMessages());
        } else {
            $result[] = $this->processSingleRecommendedItem($attribute, $recommendedValue->getResult());
        }
    }

    private function handleRecommendedMode(
        \M2E\Temu\Model\Category\CategoryAttribute $attribute,
        array &$result
    ): void {
        foreach ($attribute->getRecommendedValue() as $valueId) {
            $result[] = $this->processSingleRecommendedItem($attribute, (int)$valueId);
        }
    }

    private function processSingleRecommendedItem(
        \M2E\Temu\Model\Category\CategoryAttribute $attribute,
        int $valueId
    ): Item {
        $specId = $this->getSpecId($attribute, $valueId);
        $parentSpecId = $specId ? null : $this->getParentSpecId($attribute);

        return $this->createAttributeItem(
            $parentSpecId,
            null,
            $specId,
            $valueId
        );
    }

    private function handleCustomValueMode(
        \M2E\Temu\Model\Category\CategoryAttribute $attribute,
        \M2E\Temu\Model\Magento\Product $magentoProduct,
        array &$result
    ): void {
        if (!empty($attribute->getCustomValue())) {
            $attributeVal = $this->descriptionRender->parseWithoutMagentoTemplate(
                $attribute->getCustomValue(),
                $magentoProduct
            );

            $result[] = $this->createAttributeItem(
                $this->getParentSpecId($attribute),
                $attributeVal
            );
        }
    }

    private function handleCustomAttributeMode(
        \M2E\Temu\Model\Category\CategoryAttribute $attribute,
        \M2E\Temu\Model\Magento\Product $magentoProduct,
        array &$result
    ): void {
        $attributeValue = $magentoProduct->getAttributeValue($attribute->getCustomAttributeValue());
        if (!empty($attributeValue)) {
            $result[] = $this->createAttributeItem(
                $this->getParentSpecId($attribute),
                $attributeValue
            );
        }
    }

    private function createAttributeItem(
        ?int $parentSpecId,
        ?string $value,
        ?int $specId = null,
        ?int $valueId = null
    ): \M2E\Temu\Model\Product\VariantSku\DataProvider\Attributes\Item {
        return new \M2E\Temu\Model\Product\VariantSku\DataProvider\Attributes\Item(
            $parentSpecId,
            $value,
            $specId,
            $valueId
        );
    }

    private function getParentSpecId(\M2E\Temu\Model\Category\CategoryAttribute $attribute): ?int
    {
        $dictionaryAttribute = $this->getDictionaryAttributeById($attribute->getAttributeId());

        if ($dictionaryAttribute === null) {
            return null;
        }

        return $dictionaryAttribute->getParentSpecId();
    }

    private function getSpecId(\M2E\Temu\Model\Category\CategoryAttribute $attribute, int $valueId): ?int
    {
        $result = null;
        $dictionaryAttribute = $this->getDictionaryAttributeById($attribute->getAttributeId());

        if ($dictionaryAttribute === null) {
            return null;
        }

        $options = $dictionaryAttribute->getValues();
        foreach ($options as $option) {
            if ((int)$option->getId() === $valueId) {
                $result = $option->getSpecId();
                break;
            }
        }

        return $result;
    }
}
