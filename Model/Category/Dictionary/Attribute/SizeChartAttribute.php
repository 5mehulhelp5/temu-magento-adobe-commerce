<?php

namespace M2E\Temu\Model\Category\Dictionary\Attribute;

use M2E\Temu\Model\Category\CategoryAttribute;

class SizeChartAttribute extends \M2E\Temu\Model\Category\Dictionary\AbstractAttribute
{
    public function getType(): string
    {
        return CategoryAttribute::ATTRIBUTE_TYPE_SIZE_CHART;
    }
}
