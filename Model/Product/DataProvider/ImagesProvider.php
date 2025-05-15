<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider;

class ImagesProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'Images';

    private string $images = '';

    public function getImages(\M2E\Temu\Model\Product $product): Images\Value
    {
        $productImageSet = $product->getDescriptionTemplateSource()->getImageSet();

        $set = [];

        foreach ($productImageSet->getAll() as $productImage) {
            $set[] = new \M2E\Temu\Model\Product\DataProvider\Images\Image($productImage->getUrl());
        }

        $this->images = $this->generateImagesHash($set);

        return new Images\Value($set);
    }

    public function getMetaData(): array
    {
        return [
            self::NICK => ['images' => $this->images],
        ];
    }

    /**
     * @param \M2E\Temu\Model\Product\DataProvider\Images\Image[] $set
     *
     * @return string
     */
    private function generateImagesHash(array $set): string
    {
        $flatImages = [];
        foreach ($set as $image) {
            $flatImages[] = $image->url;
        }

        sort($flatImages);

        return \M2E\Core\Helper\Data::md5String(json_encode($flatImages));
    }
}
