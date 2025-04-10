<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Validator;

class ImagesValidator implements ValidatorInterface
{
    public function validate(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\Configurator $configurator
    ): ?string {
        if (!$configurator->isImagesAllowed()) {
            return null;
        }

        $images = $product->getDataProvider()->getImages()->getValue();

        if (count($images) === 0) {
            return (string)__(
                'Product Images are missing. To list the Product, ' .
                'please make sure that the Image settings in the Description policy are correct and the Images ' .
                'are available in the Magento Product.'
            );
        }

        foreach ($images as $image) {
            if (!$this->isValidUrl($image)) {
                return (string)__(
                    'Product Images are invalid. To list the Product, ' .
                    'please make sure that the Image settings in the Description policy are correct and the Images ' .
                    'are available in the Magento Product.'
                );
            }
        }

        return null;
    }

    private function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
