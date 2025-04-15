<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Attribute\Recommended;

use M2E\Temu\Model\Category\Dictionary\Attribute\Value;

class RetrieveValue
{
    public function retrieveValue(
        \M2E\Temu\Model\Category\CategoryAttribute $categoryAttribute,
        \M2E\Temu\Model\Category\Dictionary\AbstractAttribute $dictionaryAttribute,
        \M2E\Temu\Model\Magento\Product $magentoProduct
    ): ?Result {
        if (empty($dictionaryAttribute->getValues()) || $dictionaryAttribute->isCustomised()) {
            return null;
        }

        $result = null;
        if ($categoryAttribute->isValueModeCustomValue()) {
            $result = $this->processValue(
                $categoryAttribute->getCustomValue(),
                $categoryAttribute->getAttributeName(),
                $dictionaryAttribute
            );
        } elseif ($categoryAttribute->isValueModeCustomAttribute()) {
            $attributeVal = $magentoProduct->getAttributeValue($categoryAttribute->getCustomAttributeValue());

            $result = $this->processValue(
                $attributeVal,
                $categoryAttribute->getAttributeName(),
                $dictionaryAttribute
            );
        }

        return $result;
    }

    private function processValue(
        string $attributeVal,
        string $attributeName,
        \M2E\Temu\Model\Category\Dictionary\AbstractAttribute $attribute
    ): Result {
        $recommended = $this->findRecommendedIdByName($attribute->getValues(), $attributeVal);
        if ($recommended) {
            return Result::createSuccess($recommended);
        }

        $message = $attribute->isRequired()
            ? $this->compileErrorMessage($attributeName, $attributeVal)
            : $this->compileWarningMessage($attributeName, $attributeVal);

        return Result::createFail($message);
    }

    /**
     * @param Value[] $values
     * @param string $name
     *
     * @return int|null
     */
    private function findRecommendedIdByName(array $values, string $name): ?int
    {
        $result = null;
        $attributeName = $this->normalizeAttributeValue($name);
        foreach ($values as $attributeValue) {
            $attributeValueName = $this->normalizeAttributeValue($attributeValue->getName());

            if ($attributeName === $attributeValueName) {
                $result = (int)$attributeValue->getId();
                break;
            }
        }

        return $result;
    }

    private function normalizeAttributeValue(string $value): string
    {
        $removePunctuation = str_replace([' ', '_', '-', '.'], '', $value);

        return strtolower($removePunctuation);
    }

    private function compileWarningMessage(string $attributeName, string $attributeValue): string
    {
        return (string)__(
            'The value: <b>%1</b> set for the attribute: <b>%2</b> does not match any of the supported options'
            . ' and was not synchronized to the channel.',
            $attributeValue,
            $attributeName
        );
    }

    private function compileErrorMessage(string $attributeName, string $attributeValue): string
    {
        return (string)__(
            'Invalid value: <b>%1</b> set for attribute: <b>%2</b>. The provided value does not match any of the '
            . 'supported options.',
            $attributeValue,
            $attributeName
        );
    }
}
