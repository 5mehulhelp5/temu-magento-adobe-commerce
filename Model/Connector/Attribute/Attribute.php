<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Connector\Attribute;

class Attribute
{
    public const SALES_TYPE = 'SALES_PROPERTY';
    public const PRODUCT_TYPE = 'PRODUCT_PROPERTY';

    private string $id;
    private string $name;
    private string $type;
    private bool $isRequired;
    private bool $isMultipleSelected;
    private array $values = [];
    private int $pid;
    private int $refPid;
    private int $templatePid;
    private ?int $parentSpecId;
    private bool $isSale;
    private bool $isCustomised;
    private string $typeFormat;
    private array $rules;

    public function __construct(
        string $id,
        string $name,
        string $type,
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
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->isRequired = $isRequired;
        $this->isCustomised = $isCustomised;
        $this->isMultipleSelected = $isMultipleSelected;
        $this->refPid = $refPid;
        $this->templatePid = $templatePid;
        $this->parentSpecId = $parentSpecId;
        $this->isSale = $isSale;
        $this->typeFormat = $typeFormat;
        $this->rules = $rules;
        $this->pid = $pid;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isProductType(): bool
    {
        return $this->type === self::PRODUCT_TYPE;
    }

    public function isSalesType(): bool
    {
        return $this->type === self::SALES_TYPE;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function isMultipleSelected(): bool
    {
        return $this->isMultipleSelected;
    }

    /**
     * @return list<array{id:string, name:string, spec_id:int|null, group_id:int|null}>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function addValue(string $id, string $name, ?int $specId, ?int $groupId): void
    {
        $this->values[] = [
            'id' => $id,
            'name' => $name,
            'spec_id' => $specId,
            'group_id' => $groupId
        ];
    }

    public function getRefPid(): int
    {
        return $this->refPid;
    }

    public function getTemplatePid(): int
    {
        return $this->templatePid;
    }

    public function getParentSpecId(): ?int
    {
        return $this->parentSpecId;
    }

    public function isSale(): bool
    {
        return $this->isSale;
    }

    public function isCustomised(): bool
    {
        return $this->isCustomised;
    }

    public function getTypeFormat(): string
    {
        return $this->typeFormat;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getPid(): int
    {
        return $this->pid;
    }
}
