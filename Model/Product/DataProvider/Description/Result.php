<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Description;

class Result extends \M2E\Temu\Model\Product\DataProvider\AbstractResult
{
    public function getValue(): Value
    {
        return $this->value;
    }
}
