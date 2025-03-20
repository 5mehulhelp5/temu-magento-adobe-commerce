<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Attributes;

class Processor
{
    use \M2E\Temu\Model\Product\DataProvider\DataBuilderHelpTrait;

    protected array $attributes;

    private \M2E\Temu\Model\Category\Attribute\Repository $attributeRepository;
    private \M2E\Temu\Helper\Module\Renderer\Description $descriptionRender;
    private \M2E\Core\Helper\Magento\Attribute $attributeHelper;

    public function __construct(
        \M2E\Temu\Model\Category\Attribute\Repository $attributeRepository,
        \M2E\Temu\Helper\Module\Renderer\Description $descriptionRender,
        \M2E\Core\Helper\Magento\Attribute $attributeHelper
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->descriptionRender = $descriptionRender;
        $this->attributeHelper = $attributeHelper;
    }

    public function execute(\M2E\Temu\Model\Product $product): array
    {
        return array_map(static function ($attribute) {
            return [
                'pid' => $attribute->getPid(),
                'ref_pid' => $attribute->getRefPid(),
                'template_pid' => $attribute->getTemplatePid(),
                'value' => $attribute->getValue(),
                'value_id' => $attribute->getValueId(),
            ];
        }, $this->getAttributesData($product));
    }

    /**
     * @param \M2E\Temu\Model\Product $listingProduct
     *
     * @return \M2E\Temu\Model\Product\DataProvider\Attributes\Item[]
     */
    private function getAttributesData(
        \M2E\Temu\Model\Product $listingProduct
    ): array {
        $result = [];
        $attributes = $this->getDictionaryAttributes($listingProduct);
        $magentoProduct = $listingProduct->getMagentoProduct();
        $this->searchNotFoundAttributes($magentoProduct);

        foreach ($attributes as $attribute) {
            $dictionaryAttribute = $this->getDictionaryAttributeById($attribute->getAttributeId());
            if ($attribute->isValueModeNone()) {
                continue;
            }

            if ($attribute->isValueModeRecommended()) {
                foreach ($attribute->getRecommendedValue() as $valueId) {
                    $result[] = new \M2E\Temu\Model\Product\DataProvider\Attributes\Item(
                        $dictionaryAttribute->getPid(),
                        $dictionaryAttribute->getRefPid(),
                        $dictionaryAttribute->getTemplatePid(),
                        null,
                        (int)$valueId,
                    );
                }
            }

            if ($attribute->isValueModeCustomValue()) {
                if (!empty($attribute->getCustomValue())) {
                    $attributeVal = $this->descriptionRender->parseWithoutMagentoTemplate(
                        $attribute->getCustomValue(),
                        $magentoProduct
                    );
                    $result[] = new \M2E\Temu\Model\Product\DataProvider\Attributes\Item(
                        $dictionaryAttribute->getPid(),
                        $dictionaryAttribute->getRefPid(),
                        $dictionaryAttribute->getTemplatePid(),
                        $attributeVal,
                        null
                    );
                }
            }

            if ($attribute->isValueModeCustomAttribute()) {
                $attributeVal = $magentoProduct->getAttributeValue($attribute->getCustomAttributeValue());
                if (!empty($attributeVal)) {
                    $result[] = new \M2E\Temu\Model\Product\DataProvider\Attributes\Item(
                        $dictionaryAttribute->getPid(),
                        $dictionaryAttribute->getRefPid(),
                        $dictionaryAttribute->getTemplatePid(),
                        $attributeVal,
                        null
                    );
                }
            }
        }

        $this->processNotFoundAttributes(
            $magentoProduct,
            $listingProduct->getListing()->getStoreId()
        );

        return $result;
    }

    /**
     * @param \M2E\Temu\Model\Product $listingProduct
     *
     * @return \M2E\Temu\Model\Category\CategoryAttribute[]
     */
    private function getDictionaryAttributes(\M2E\Temu\Model\Product $listingProduct): array
    {
        $categoryId = $listingProduct->getTemplateCategoryId();
        $dictionary = $listingProduct->getCategoryDictionary();
        foreach ($dictionary->getProductAttributes() as $attribute) {
            $this->attributes[$attribute->getId()] = $attribute;
        }

        return $this->attributeRepository->findByDictionaryId(
            $categoryId,
            [\M2E\Temu\Model\Category\CategoryAttribute::ATTRIBUTE_TYPE_PRODUCT]
        );
    }

    private function getDictionaryAttributeById(
        string $attributeId
    ): ?\M2E\Temu\Model\Category\Dictionary\Attribute\ProductAttribute {
        return $this->attributes[$attributeId] ?? null;
    }

    private function getWarningTitle(): string
    {
        return (string)__('Product');
    }

    private function processNotFoundAttributes(
        \M2E\Temu\Model\Magento\Product $magentoProduct,
        int $storeId
    ): void {
        $attributes = $magentoProduct->getNotFoundAttributes();
        if (!empty($attributes)) {
            $this->addNotFoundAttributesMessages($attributes, $storeId);
        }
    }

    private function addNotFoundAttributesMessages(array $attributes, int $storeId): void
    {
        $attributesTitles = [];

        foreach ($attributes as $attribute) {
            $attributesTitles[] = $this->attributeHelper
                ->getAttributeLabel(
                    $attribute,
                    $storeId,
                );
        }

        $this->addWarningMessage(
            (string)__(
                '%1: Attribute(s) %2 were not found' .
                ' in this Product and its value was not sent.',
                $this->getWarningTitle(),
                implode(', ', $attributesTitles),
            ),
        );
    }
}
