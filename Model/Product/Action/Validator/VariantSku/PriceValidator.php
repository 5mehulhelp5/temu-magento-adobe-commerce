<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Validator\VariantSku;

class PriceValidator implements ValidatorInterface
{
    public function validate(\M2E\Temu\Model\Product\VariantSku $variant): ?string
    {
        if ($variant->getDataProvider()->getPrice()->getValue()->price === 0.0) {
            return (string)__(
                'The Product Price cannot be 0. Please enter a valid Price greater than 0 to update your Product on the channel.'
            );
        }

        return null;
    }
}
