<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Dictionary\Attribute;

class Value
{
    private string $id;
    private string $name;
    private ?int $specId;
    private ?int $groupId;

    public function __construct(
        string $id,
        string $name,
        ?int $specId,
        ?int $groupId
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->specId = $specId;
        $this->groupId = $groupId;
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
}
