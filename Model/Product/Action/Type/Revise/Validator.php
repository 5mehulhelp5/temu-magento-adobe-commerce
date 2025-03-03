<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\Revise;

class Validator extends \M2E\Temu\Model\Product\Action\Type\AbstractValidator
{
    private \M2E\Temu\Model\Product\Action\Validator\VariantValidator $variantValidator;

    public function __construct(
        \M2E\Temu\Model\Product\Action\Validator\VariantValidator $variantValidator
    ) {
        $this->variantValidator = $variantValidator;
    }

    public function validate(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\Configurator $actionConfigurator,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings
    ): bool {
        if (!$product->isRevisable()) {
            $this->addMessage('Item is not Listed or not available');

            return false;
        }

        if (empty($product->getChannelProductId())) {
            return false;
        }

        $variantErrors = $this->variantValidator->validate($product, $variantSettings);
        foreach ($variantErrors as $variantError) {
            $this->addMessage($variantError);
        }

        return !$this->hasErrorMessages();
    }
}
