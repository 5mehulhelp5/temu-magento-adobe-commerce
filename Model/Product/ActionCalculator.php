<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product;

class ActionCalculator
{
    private VariantSku\ActionCalculator $variantActionCalculator;

    public function __construct(
        VariantSku\ActionCalculator $variantActionCalculator
    ) {
        $this->variantActionCalculator = $variantActionCalculator;
    }

    public function calculate(\M2E\Temu\Model\Product $product, bool $force, int $change): Action
    {
        if ($product->isStatusNotListed()) {
            return $this->calculateToList($product);
        }

        if ($product->isStatusListed()) {
            return $this->calculateToReviseOrStop($product);
        }

        if ($product->isStatusInactive()) {
            return $this->calculateToRelist($product, $change);
        }

        return Action::createNothing($product);
    }

    public function calculateToList(\M2E\Temu\Model\Product $product): Action
    {
        if (
            !$product->isListable()
            || !$product->isStatusNotListed()
        ) {
            return Action::createNothing($product);
        }

        if (!$this->isNeedListProduct($product)) {
            return Action::createNothing($product);
        }

        $variantSettings = $this->calculateVariants($product, false);
        if (!$variantSettings->hasAddAction()) {
            return Action::createNothing($product);
        }

        $configurator = new \M2E\Temu\Model\Product\Action\Configurator();
        $configurator->enableAll();

        return Action::createList($product, $configurator, $variantSettings);
    }

    private function isNeedListProduct(\M2E\Temu\Model\Product $product): bool
    {
        $syncPolicy = $product->getSynchronizationTemplate();

        if (!$syncPolicy->isListMode()) {
            return false;
        }

        if (
            $syncPolicy->isListStatusEnabled()
            && !$product->getMagentoProduct()->isStatusEnabled()
        ) {
            return false;
        }

        if (
            $syncPolicy->isListIsInStock()
            && !$product->getMagentoProduct()->isStockAvailability()
        ) {
            return false;
        }

        return true;
    }

    public function calculateToReviseOrStop(
        \M2E\Temu\Model\Product $product,
        $needForceRevise = false
    ): Action {
        if (
            !$product->isRevisable()
            && !$product->isStoppable()
        ) {
            return Action::createNothing($product);
        }

        if ($this->isNeedStopProduct($product)) {
            return Action::createStop($product);
        }

        $variantSettings = $this->calculateVariants($product, $needForceRevise);
        if ($variantSettings->isAllStopAction()) {
            return Action::createStop($product);
        }

        $configurator = new \M2E\Temu\Model\Product\Action\Configurator();
        $configurator->disableAll();

        $this->updateConfiguratorAddVariants($configurator, $variantSettings);

        if (empty($configurator->getAllowedDataTypes())) {
            return Action::createNothing($product);
        }

        return Action::createRevise($product, $configurator, $variantSettings);
    }

    private function isNeedStopProduct(\M2E\Temu\Model\Product $product): bool
    {
        $syncPolicy = $product->getSynchronizationTemplate();

        if (!$syncPolicy->isStopMode()) {
            return false;
        }

        if (
            $syncPolicy->isStopStatusDisabled()
            && !$product->getMagentoProduct()->isStatusEnabled()
        ) {
            return true;
        }

        if (
            $syncPolicy->isStopOutOfStock()
            && !$product->getMagentoProduct()->isStockAvailability()
        ) {
            return true;
        }

        return false;
    }

    // ----------------------------------------

    public function calculateToRelist(\M2E\Temu\Model\Product $product, int $changer): Action
    {
        if (!$product->isRelistable()) {
            return Action::createNothing($product);
        }

        if (!$this->isNeedRelistProduct($product, $changer)) {
            return Action::createNothing($product);
        }

        $variantSettings = $this->calculateVariants($product, false);
        if (!$variantSettings->hasAddAction()) {
            return Action::createNothing($product);
        }

        $configurator = new \M2E\Temu\Model\Product\Action\Configurator();
        $configurator->enableAll();

        return Action::createRelist($product, $configurator, $variantSettings);
    }

    private function isNeedRelistProduct(\M2E\Temu\Model\Product $product, int $changer): bool
    {
        $syncPolicy = $product->getSynchronizationTemplate();

        if (!$syncPolicy->isRelistMode()) {
            return false;
        }

        if (
            $product->isStatusInactive()
            && $syncPolicy->isRelistFilterUserLock()
            && $product->isStatusChangerUser()
            && $changer !== \M2E\Temu\Model\Product::STATUS_CHANGER_USER
        ) {
            return false;
        }

        if (
            $syncPolicy->isRelistStatusEnabled()
            && !$product->getMagentoProduct()->isStatusEnabled()
        ) {
            return false;
        }

        if (
            $syncPolicy->isRelistIsInStock()
            && !$product->getMagentoProduct()->isStockAvailability()
        ) {
            return false;
        }

        return true;
    }

    private function updateConfiguratorAddVariants(
        \M2E\Temu\Model\Product\Action\Configurator $configurator,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings
    ): void {
        if (
            $variantSettings->hasAddAction()
            || $variantSettings->hasReviseAction()
        ) {
            $configurator->allowVariants();

            return;
        }

        $configurator->disallowVariants();
    }

    private function calculateVariants(
        \M2E\Temu\Model\Product $product,
        bool $needForceRevise
    ): \M2E\Temu\Model\Product\Action\VariantSettings {
        $variantSettingsBuilder = new \M2E\Temu\Model\Product\Action\VariantSettingsBuilder(
            $needForceRevise
        );
        foreach ($product->getVariants() as $variant) {
            $action = $this->variantActionCalculator->process($variant);

            $variantSettingsBuilder->add($variant->getId(), $action, $variant->getStatus());
        }

        return $variantSettingsBuilder->build();
    }
}
