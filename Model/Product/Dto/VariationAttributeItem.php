<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Dto;

class VariationAttributeItem
{
    private string $attributeCode;
    private string $name;

    public function __construct(string $attributeCode, string $name)
    {
        if (empty($attributeCode)) {
            throw new \M2E\Temu\Model\Exception\Logic('Attribute code must not be empty');
        }

        if (empty($name)) {
            throw new \M2E\Temu\Model\Exception\Logic('Attribute name must not be empty');
        }

        $this->attributeCode = $attributeCode;
        $this->name = $name;
    }

    public function getAttributeCode(): string
    {
        return $this->attributeCode;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
