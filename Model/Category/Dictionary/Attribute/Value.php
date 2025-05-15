<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Dictionary\Attribute;

class Value
{
    private string $id;
    private string $name;
    private ?int $specId;
    private ?int $groupId;
    /** @var \M2E\Temu\Model\Category\Dictionary\Attribute\ValueRelation[] */
    private array $valueRelation;

    /**
     * @param string $id
     * @param string $name
     * @param int|null $specId
     * @param int|null $groupId
     * @param \M2E\Temu\Model\Category\Dictionary\Attribute\ValueRelation[] $valueRelation
     */
    public function __construct(
        string $id,
        string $name,
        ?int $specId,
        ?int $groupId,
        array $valueRelation
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->specId = $specId;
        $this->groupId = $groupId;
        $this->valueRelation = $valueRelation;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSpecId(): ?int
    {
        return $this->specId;
    }

    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    /**
     * @return \M2E\Temu\Model\Category\Dictionary\Attribute\ValueRelation[]
     */
    public function getValueRelation(): array
    {
        return $this->valueRelation;
    }
}
