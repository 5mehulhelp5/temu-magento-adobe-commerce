<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\DataProvider\Attributes;

class Item
{
    private ?int $parentSpecId;
    private ?string $value;
    private ?string $name;
    private ?int $specId;
    private ?int $valueId;

    public function __construct(
        ?int $parentSpecId,
        ?string $value,
        ?string $name,
        ?int $specId,
        ?int $valueId
    ) {
        $this->parentSpecId = $parentSpecId;
        $this->value = $value;
        $this->specId = $specId;
        $this->name = $name;
        $this->valueId = $valueId;
    }

    public function getParentSpecId(): ?int
    {
        return $this->parentSpecId;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSpecId(): ?int
    {
        return $this->specId;
    }

    public function getValueId(): ?int
    {
        return $this->valueId;
    }
}
