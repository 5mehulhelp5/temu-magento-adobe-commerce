<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Dictionary\Attribute;

class ValueRelation
{
    private int $childTemplatePid;
    /** @var int[] */
    private array $valueIds;

    /**
     * @param int $childTemplatePid
     * @param int[] $valueIds
     */
    public function __construct(
        int $childTemplatePid,
        array $valueIds
    ) {
        $this->childTemplatePid = $childTemplatePid;
        $this->valueIds = $valueIds;
    }

    public function getChildTemplatePid(): int
    {
        return $this->childTemplatePid;
    }

    /**
     * @return int[]
     */
    public function getValueIds(): array
    {
        return $this->valueIds;
    }
}
