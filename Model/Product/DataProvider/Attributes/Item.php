<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Attributes;

class Item
{
    private int $pid;
    private int $refPid;
    private int $templatePid;
    private ?string $value;
    private ?int $valueId;

    public function __construct(
        int $pid,
        int $refPid,
        int $templatePid,
        ?string $value,
        ?int $valueId
    ) {
        $this->refPid = $refPid;
        $this->templatePid = $templatePid;
        $this->value = $value;
        $this->valueId = $valueId;
        $this->pid = $pid;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getRefPid(): int
    {
        return $this->refPid;
    }

    public function getTemplatePid(): int
    {
        return $this->templatePid;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getValueId(): ?int
    {
        return $this->valueId;
    }
}
