<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Validator;

class EanValidator implements ValidatorInterface
{
    private \M2E\Temu\Model\Settings $settings;

    public function __construct(
        \M2E\Temu\Model\Settings $settings
    ) {
        $this->settings = $settings;
    }

    public function validate(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\Configurator $configurator
    ): ?string {
        $eanAttributeCode = $this->settings->getIdentifierCodeValue();
        $magentoProduct = $product->getMagentoProduct();

        if (!$magentoProduct->getAttributeValue($eanAttributeCode)) {
            return (string)__('EAN is missing a value.');
        }

        return null;
    }
}
