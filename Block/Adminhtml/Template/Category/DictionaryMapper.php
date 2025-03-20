<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Template\Category;

use M2E\Temu\Model\Category\CategoryAttribute;

class DictionaryMapper
{
    private \M2E\Temu\Model\Category\Attribute\Repository $attributeRepository;

    public function __construct(
        \M2E\Temu\Model\Category\Attribute\Repository $attributeRepository
    ) {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @see \M2E\Temu\Block\Adminhtml\Template\Category\Chooser\Specific\Form\Element\Dictionary
     */
    public function getProductAttributes(\M2E\Temu\Model\Category\Dictionary $dictionary): array
    {
        $savedAttributes = $this->loadSavedAttributes($dictionary, [
            CategoryAttribute::ATTRIBUTE_TYPE_PRODUCT,
        ]);

        $attributes = [];
        foreach ($dictionary->getProductAttributes() as $productAttribute) {
            $item = $this->map($productAttribute, $savedAttributes);

            if ($item['required']) {
                array_unshift($attributes, $item);
                continue;
            }

            $attributes[] = $item;
        }

        return $this->sortAttributesByTitle($attributes);
    }

    /**
     * @see \M2E\Temu\Block\Adminhtml\Template\Category\Chooser\Specific\Form\Element\Dictionary
     */
    public function getSalesAttributes(\M2E\Temu\Model\Category\Dictionary $dictionary): array
    {
        $savedAttributes = $this->loadSavedAttributes($dictionary, [
            CategoryAttribute::ATTRIBUTE_TYPE_SALES,
        ]);

        $attributes = [];
        foreach ($dictionary->getSalesAttributes() as $productAttribute) {
            $item = $this->map($productAttribute, $savedAttributes);

            if ($item['required']) {
                array_unshift($attributes, $item);
                continue;
            }

            $attributes[] = $item;
        }

        return $this->sortAttributesByTitle($attributes);
    }

    public function getVirtualAttributes(\M2E\Temu\Model\Category\Dictionary $dictionary): array
    {
        $savedAttributes = $this->loadSavedAttributes($dictionary, [
            CategoryAttribute::ATTRIBUTE_TYPE_BRAND,
            CategoryAttribute::ATTRIBUTE_TYPE_SIZE_CHART,
        ]);

        $attributes = [];
        foreach ($dictionary->getBrandAndSizeChartAttributes() as $virtualAttribute) {
            $item = $this->map($virtualAttribute, $savedAttributes);

            if ($item['required']) {
                array_unshift($attributes, $item);
                continue;
            }

            $attributes[] = $item;
        }

        return $this->sortAttributesByTitle($attributes);
    }

    private function map(
        \M2E\Temu\Model\Category\Dictionary\AbstractAttribute $attribute,
        array $savedAttributes
    ): array {
        $item = [
            'id' => $attribute->getId(),
            'title' => $attribute->getName(),
            'attribute_type' => $attribute->getType(),
            'type' => $attribute->isMultipleSelected() ? 'select_multiple' : 'select',
            'required' => $attribute->isRequired(),
            'min_values' => $attribute->isRequired() ? 1 : 0,
            'max_values' => $attribute->isMultipleSelected() ? count($attribute->getValues()) : 1,
            'values' => [],
            'template_attribute' => []
        ];

        $existsAttribute = $savedAttributes[$attribute->getId()] ?? null;
        if ($existsAttribute) {
            $item['template_attribute'] = [
                'id' => $existsAttribute->getAttributeId(),
                'template_category_id' => $existsAttribute->getId(),
                'mode' => '1',
                'attribute_title' => $existsAttribute->getAttributeId(),
                'value_mode' => $existsAttribute->getValueMode(),
                'value_temu_recommended' => $existsAttribute->getRecommendedValue(),
                'value_custom_value' => $existsAttribute->getCustomValue(),
                'value_custom_attribute' => $existsAttribute->getCustomAttributeValue(),
            ];
        }

        foreach ($attribute->getValues() as $value) {
            $item['values'][] = [
                'id' => $value->getId(),
                'value' => $value->getName(),
            ];
        }

        return $item;
    }

    private function loadSavedAttributes(
        \M2E\Temu\Model\Category\Dictionary $dictionary,
        array $typeFilter = []
    ): array {
        $attributes = [];

        $savedAttributes = $this
            ->attributeRepository
            ->findByDictionaryId($dictionary->getId(), $typeFilter);

        foreach ($savedAttributes as $attribute) {
            $attributes[$attribute->getAttributeId()] = $attribute;
        }

        return $attributes;
    }

    public function sortAttributesByTitle(array $attributes): array
    {
        usort($attributes, function ($prev, $next) {
            return strcmp($prev['title'], $next['title']);
        });

        $requiredAttributes = [];
        foreach ($attributes as $index => $attribute) {
            if (isset($attribute['required']) && $attribute['required'] === true) {
                $requiredAttributes[] = $attribute;
                unset($attributes[$index]);
            }
        }

        return array_merge($requiredAttributes, $attributes);
    }
}
