<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Images;

class Value
{
    /** @var \M2E\Temu\Model\Product\DataProvider\Images\Image[] */
    public array $set;
    public string $imagesHash;

    public function __construct(
        array $set,
        string $imagesHash
    ) {
        $this->set = $set;
        $this->imagesHash = $imagesHash;
    }
}
