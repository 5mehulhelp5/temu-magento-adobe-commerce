<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Category\Dictionary\Attribute;

use M2E\Temu\Model\Category\CategoryAttribute;

class ProductAttribute extends \M2E\Temu\Model\Category\Dictionary\AbstractAttribute
{
    public function getType(): string
    {
        return CategoryAttribute::ATTRIBUTE_TYPE_PRODUCT;
    }
}
