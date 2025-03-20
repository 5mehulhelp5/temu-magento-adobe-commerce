<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Images;

class Result extends \M2E\Temu\Model\Product\DataProvider\AbstractResult
{
    public function getValue(): array
    {
        return $this->value;
    }
}
