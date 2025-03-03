<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\Delete;

class Validator extends \M2E\Temu\Model\Product\Action\Type\AbstractValidator
{
    private \M2E\Temu\Model\Product\RemoveHandler $removeHandler;

    public function __construct(
        \M2E\Temu\Model\Product\RemoveHandler $removeHandler
    ) {
        $this->removeHandler = $removeHandler;
    }

    public function validate(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\Configurator $actionConfigurator,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings
    ): bool {
        if (!$this->getListingProduct()->isStoppable()) {
            $this->removeHandler->process($this->getListingProduct());

            return false;
        }

        return true;
    }
}
