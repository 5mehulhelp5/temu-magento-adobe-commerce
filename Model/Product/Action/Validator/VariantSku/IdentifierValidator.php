<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Validator\VariantSku;

class IdentifierValidator implements ValidatorInterface
{
    public function validate(\M2E\Temu\Model\Product\VariantSku $variant): ?string
    {
        if (empty($variant->getDataProvider()->getIdentifier()->getValue())) {
            return (string)__(
                'EAN is missing a value'
            );
        }

        return null;
    }
}
