<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider;

class ImagesProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'Images';

    private string $images = '';

    public function getImages(\M2E\Temu\Model\Product $product): array
    {
        $productImageSet = $product->getDescriptionTemplateSource()->getImageSet();

        $result = [];

        foreach ($productImageSet->getAll() as $productImage) {
            $result[] = $productImage->getUrl();
        }

        $data = json_encode($result);
        $this->images =  \M2E\Core\Helper\Data::md5String($data);

        return $result;
    }

    public function getMetaData(): array
    {
        return [
            self::NICK => ['images' => $this->images],
        ];
    }
}
