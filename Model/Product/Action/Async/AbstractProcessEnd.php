<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Async;

use M2E\Temu\Model\Product\Action\ActionLoggerTrait;

abstract class AbstractProcessEnd
{
    use ActionLoggerTrait;

    private \M2E\Temu\Model\Product\LockManager $lockManager;
    private \M2E\Temu\Model\Product $listingProduct;
    private \M2E\Temu\Model\Product\Action\Configurator $configurator;
    private \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings;
    private array $params;
    private array $requestMetadata;
    private int $statusChanger;

    private array $warningMessages;

    public function initialize(
        \M2E\Temu\Model\Product\Action\Logger $actionLogger,
        \M2E\Temu\Model\Product\LockManager $lockManager,
        \M2E\Temu\Model\Product $listingProduct,
        \M2E\Temu\Model\Product\Action\Configurator $configurator,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings,
        \M2E\Temu\Model\Product\Action\LogBuffer $logBuffer,
        array $params,
        array $requestMetadata,
        int $statusChanger,
        array $warningMessages
    ): void {
        $this->actionLogger = $actionLogger;
        $this->lockManager = $lockManager;
        $this->listingProduct = $listingProduct;
        $this->configurator = $configurator;
        $this->variantSettings = $variantSettings;
        $this->logBuffer = $logBuffer;
        $this->params = $params;
        $this->requestMetadata = $requestMetadata;
        $this->statusChanger = $statusChanger;

        foreach ($warningMessages as $warningMessage) {
            $this->getLogBuffer()->addWarning($warningMessage);
        }
    }

    public function process(array $resultData, array $messages): void
    {
        try {
            $this->processComplete($resultData, $messages);
        } finally {
            $this->flushActionLogs();
            $this->lockManager->unlock($this->listingProduct);
        }
    }

    abstract protected function processComplete(array $resultData, array $messages): void;

    protected function getListingProduct(): \M2E\Temu\Model\Product
    {
        return $this->listingProduct;
    }

    protected function getActionConfigurator(): \M2E\Temu\Model\Product\Action\Configurator
    {
        return $this->configurator;
    }

    protected function getVariantSettings(): \M2E\Temu\Model\Product\Action\VariantSettings
    {
        return $this->variantSettings;
    }

    protected function getParams(): array
    {
        return $this->params;
    }

    protected function getRequestMetadata(): array
    {
        return $this->requestMetadata;
    }

    protected function getStatusChanger(): int
    {
        return $this->statusChanger;
    }
}
