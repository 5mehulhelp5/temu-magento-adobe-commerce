<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Validator;

class VariantValidator
{
    protected const VARIATION_COUNT_MAXIMUM = 100;

    private \M2E\Temu\Model\Product\Action\Validator\VariantSku\PriceValidator $priceValidator;
    private \M2E\Temu\Model\Product\Action\Validator\VariantSku\QtyValidator $qtyValidator;
    private \M2E\Temu\Model\Product\Action\Validator\VariantSku\SameSkuAlreadyExists $sameSkuAlreadyExists;
    private \M2E\Temu\Model\Product\Action\Validator\VariantSku\IdentifierValidator $identifierValidator;

    public function __construct(
        \M2E\Temu\Model\Product\Action\Validator\VariantSku\PriceValidator $priceValidator,
        \M2E\Temu\Model\Product\Action\Validator\VariantSku\QtyValidator $qtyValidator,
        \M2E\Temu\Model\Product\Action\Validator\VariantSku\SameSkuAlreadyExists $sameSkuAlreadyExists,
        \M2E\Temu\Model\Product\Action\Validator\VariantSku\IdentifierValidator $identifierValidator
    ) {
        $this->priceValidator = $priceValidator;
        $this->qtyValidator = $qtyValidator;
        $this->sameSkuAlreadyExists = $sameSkuAlreadyExists;
        $this->identifierValidator = $identifierValidator;
    }

    public function validate(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings
    ): array {
        $variantsWithoutSkipped = [];
        $messages = [];

        foreach ($product->getVariants() as $variant) {
            if (
                $variantSettings->isSkipAction($variant->getId())
                || $variantSettings->isStopAction($variant->getId())
            ) {
                continue;
            }

            $variantsWithoutSkipped[] = $variant;
        }

        if (count($variantsWithoutSkipped) > self::VARIATION_COUNT_MAXIMUM) {
            $messages[] = sprintf(
                'The number of product variations cannot exceed %s.',
                self::VARIATION_COUNT_MAXIMUM
            );

            return $messages;
        }

        foreach ($variantsWithoutSkipped as $variant) {
            $variantHasError = false;

            if ($error = $this->priceValidator->validate($variant)) {
                $messages[] = $error;
                $variantHasError = true;
            }

            if ($error = $this->qtyValidator->validate($variant)) {
                $messages[] = $error;
                $variantHasError = true;
            }

            if (
                $variantSettings->isAddAction($variant->getId())
            ) {
                if ($error = $this->sameSkuAlreadyExists->validate($variant)) {
                    $messages[] = $error;
                    $variantHasError = true;
                }

                if ($error = $this->identifierValidator->validate($variant)) {
                    $messages[] = $error;
                    $variantHasError = true;
                }
            }

            if ($variantHasError) {
                return $messages;
            }
        }

        return [];
    }
}
