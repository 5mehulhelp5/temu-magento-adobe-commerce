<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Variants;

class Result extends \M2E\Temu\Model\Product\DataProvider\AbstractResult
{
    public function getValue(): \M2E\Temu\Model\Product\DataProvider\Variants\Collection
    {
        return $this->value;
    }
}
