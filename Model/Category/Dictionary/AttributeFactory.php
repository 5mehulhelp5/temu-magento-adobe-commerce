<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Dictionary;

class AttributeFactory
{
    public function createSalesAttribute(
        string $id,
        string $name,
        bool $isSale,
        bool $isRequired,
        bool $isCustomised,
        bool $isMultipleSelected,
        string $typeFormat,
        array $rules,
        int $pid,
        int $refPid,
        int $templatePid,
        ?int $parentSpecId
    ): \M2E\Temu\Model\Category\Dictionary\Attribute\SalesAttribute {
        return new \M2E\Temu\Model\Category\Dictionary\Attribute\SalesAttribute(
            $id,
            $name,
            $isSale,
            $isRequired,
            $isCustomised,
            $isMultipleSelected,
            $typeFormat,
            $rules,
            $pid,
            $refPid,
            $templatePid,
            $parentSpecId
        );
    }

    /**
     * @param \M2E\Temu\Model\Category\Dictionary\Attribute\Value[] $values
     */
    public function createProductAttribute(
        string $id,
        string $name,
        bool $isSale,
        bool $isRequired,
        bool $isCustomised,
        bool $isMultipleSelected,
        string $typeFormat,
        array $rules,
        int $pid,
        int $refPid,
        int $templatePid,
        ?int $parentSpecId,
        array $values
    ): \M2E\Temu\Model\Category\Dictionary\Attribute\ProductAttribute {
        return new \M2E\Temu\Model\Category\Dictionary\Attribute\ProductAttribute(
            $id,
            $name,
            $isSale,
            $isRequired,
            $isCustomised,
            $isMultipleSelected,
            $typeFormat,
            $rules,
            $pid,
            $refPid,
            $templatePid,
            $parentSpecId,
            $values
        );
    }

    public function createValue(string $id, string $name, ?int $specId, ?int $groupId): \M2E\Temu\Model\Category\Dictionary\Attribute\Value
    {
        return new \M2E\Temu\Model\Category\Dictionary\Attribute\Value($id, $name, $specId, $groupId);
    }
}
