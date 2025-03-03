<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\Relist;

class Request extends \M2E\Temu\Model\Product\Action\Type\Revise\Request
{
    public function getActionData(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\Configurator $actionConfigurator,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings,
        array $params
    ): array {
        $actionConfigurator->enableAll();

        return parent::getActionData(
            $product,
            $actionConfigurator,
            $variantSettings,
            $params
        );
    }
}
