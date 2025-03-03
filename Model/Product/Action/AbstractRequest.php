<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action;

abstract class AbstractRequest
{
    private RequestData $requestData;
    private LogBuffer $logBuffer;
    private array $metadata = [];

    // ----------------------------------------

    public function build(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\Configurator $actionConfigurator,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings,
        LogBuffer $logBuffer,
        array $params
    ): RequestData {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->requestData)) {
            return $this->requestData;
        }

        $this->logBuffer = $logBuffer;
        $data = $this->getActionData(
            $product,
            $actionConfigurator,
            $variantSettings,
            $params
        );
        $this->metadata = $this->getActionMetadata();

        $requestData = new RequestData($data);

        return $this->requestData = $requestData;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    // ----------------------------------------

    abstract protected function getActionData(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\Configurator $actionConfigurator,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings,
        array $params
    ): array;

    abstract protected function getActionMetadata(): array;

    // ----------------------------------------

    protected function addWarningMessage(string $message): void
    {
        $this->getLogBuffer()->addWarning($message);
    }

    protected function getLogBuffer(): LogBuffer
    {
        return $this->logBuffer;
    }
}
