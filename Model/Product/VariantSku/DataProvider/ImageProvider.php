<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\DataProvider;

use M2E\Temu\Model\Product\DataProvider\DataBuilderHelpTrait;
use M2E\Temu\Model\Product\DataProvider\DataBuilderInterface;

class ImageProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'Image';

    public function getImage(\M2E\Temu\Model\Product\VariantSku $variantSku): string
    {
        $mainImageSource = $variantSku->getProduct()->getDescriptionTemplate()->getImageMainSource();

        if ($mainImageSource['mode'] === \M2E\Temu\Model\Policy\Description::IMAGE_MAIN_MODE_PRODUCT) {
            $imageAttributeCode = 'image';
        } else {
            $imageAttributeCode = $mainImageSource['attribute'];
        }

        return $variantSku->getProduct()->getMagentoProduct()->getImage($imageAttributeCode)->getUrl();
    }
}
