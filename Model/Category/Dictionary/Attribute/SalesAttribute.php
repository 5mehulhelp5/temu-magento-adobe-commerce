<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Dictionary\Attribute;

class SalesAttribute extends \M2E\Temu\Model\Category\Dictionary\AbstractAttribute
{
    public function getType(): string
    {
        return \M2E\Temu\Model\Category\CategoryAttribute::ATTRIBUTE_TYPE_SALES;
    }
}
