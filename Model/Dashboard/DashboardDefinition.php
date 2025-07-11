<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Dashboard;

class DashboardDefinition implements \M2E\Core\Model\Dashboard\DashboardDefinitionInterface
{
    private const NAME = 'm2e_temu';
    private const TITLE = 'M2E Temu';

    private \M2E\Temu\Model\Dashboard\Shipments\DefinitionFactory $shipmentDefinitionFactory;
    private \M2E\Temu\Model\Dashboard\Shipments\Definition $shipmentDefinition;
    private \M2E\Temu\Model\Dashboard\Errors\DefinitionFactory $errorsDefinitionFactory;
    private \M2E\Temu\Model\Dashboard\Errors\Definition $errorsDefinition;
    private \M2E\Temu\Model\Dashboard\ProductIssues\DefinitionFactory $productIssuesDefinitionFactory;
    private \M2E\Temu\Model\Dashboard\ProductIssues\Definition $productIssuesDefinition;
    private \M2E\Temu\Model\Dashboard\Products\DefinitionFactory $productsDefinitionFactory;
    private \M2E\Temu\Model\Dashboard\Products\Definition $productsDefinition;
    private \M2E\Temu\Model\Dashboard\Sales\DefinitionFactory $salesDefinitionFactory;
    private \M2E\Temu\Model\Dashboard\Sales\Definition $salesDefinition;

    public function __construct(
        \M2E\Temu\Model\Dashboard\Shipments\DefinitionFactory $shipmentDefinitionFactory,
        \M2E\Temu\Model\Dashboard\Errors\DefinitionFactory $errorsDefinitionFactory,
        \M2E\Temu\Model\Dashboard\ProductIssues\DefinitionFactory $productIssuesDefinitionFactory,
        \M2E\Temu\Model\Dashboard\Products\DefinitionFactory $productsDefinitionFactory,
        \M2E\Temu\Model\Dashboard\Sales\DefinitionFactory $salesDefinitionFactory
    ) {
        $this->salesDefinitionFactory = $salesDefinitionFactory;
        $this->productIssuesDefinitionFactory = $productIssuesDefinitionFactory;
        $this->errorsDefinitionFactory = $errorsDefinitionFactory;
        $this->shipmentDefinitionFactory = $shipmentDefinitionFactory;
        $this->productsDefinitionFactory = $productsDefinitionFactory;
    }

    public function getModuleName(): string
    {
        return self::NAME;
    }

    public function getModuleTitle(): string
    {
        return self::TITLE;
    }

    public function getShipmentDefinition(): \M2E\Core\Model\Dashboard\Shipments\DefinitionInterface
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->shipmentDefinition)) {
            $this->shipmentDefinition = $this->shipmentDefinitionFactory->create();
        }

        return $this->shipmentDefinition;
    }

    public function getErrorsDefinition(): \M2E\Core\Model\Dashboard\Errors\DefinitionInterface
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->errorsDefinition)) {
            $this->errorsDefinition = $this->errorsDefinitionFactory->create();
        }

        return $this->errorsDefinition;
    }

    public function getProductIssuesDefinition(): \M2E\Core\Model\Dashboard\ProductIssues\DefinitionInterface
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->productIssuesDefinition)) {
            $this->productIssuesDefinition = $this->productIssuesDefinitionFactory->create();
        }

        return $this->productIssuesDefinition;
    }

    public function getProductsDefinition(): \M2E\Core\Model\Dashboard\Products\DefinitionInterface
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->productsDefinition)) {
            $this->productsDefinition = $this->productsDefinitionFactory->create();
        }

        return $this->productsDefinition;
    }

    public function getSalesDefinition(): \M2E\Core\Model\Dashboard\Sales\DefinitionInterface
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->salesDefinition)) {
            $this->salesDefinition = $this->salesDefinitionFactory->create();
        }

        return $this->salesDefinition;
    }
}
