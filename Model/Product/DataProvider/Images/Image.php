<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Images;

class Image
{
    public string $url;

    public function __construct(
        string $url
    ) {
        $this->url = $url;
    }
}
