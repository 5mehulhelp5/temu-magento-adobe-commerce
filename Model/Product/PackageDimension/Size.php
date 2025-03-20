<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\PackageDimension;

class Size
{
    private float $length;
    private float $width;
    private float $height;
    private string $unit;

    public function __construct(float $length, float $width, float $height, string $unit)
    {
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->unit = $unit;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getVolumeWeight(): string
    {
        return (string)round($this->length * $this->width * $this->height, 4);
    }
}
