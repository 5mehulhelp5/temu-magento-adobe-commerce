<?php

declare(strict_types=1);

namespace M2E\Temu\Model\AttributeMapping\Gpsr\CategoryModifier;

class CategoryDiffStub extends \M2E\Temu\Model\ActiveRecord\Diff
{
    public function isDifferent(): bool
    {
        return true;
    }

    public function isCategoriesDifferent(): bool
    {
        return true;
    }
}
