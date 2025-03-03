<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type;

abstract class AbstractResponse
{
    private array $params = [];
    private array $requestMetaData = [];
    private array $responseData = [];
    private \M2E\Temu\Model\Product $listingProduct;
    private \M2E\Temu\Model\Product\Action\Configurator $configurator;
    private \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings;
    private \M2E\Temu\Model\Product\Action\LogBuffer $logBuffer;
    private int $statusChanger;
    private \M2E\Temu\Model\Tag\ListingProduct\Buffer $tagBuffer;
    private \M2E\Temu\Model\TagFactory $tagFactory;

    public function __construct(
        \M2E\Temu\Model\Tag\ListingProduct\Buffer $tagBuffer,
        \M2E\Temu\Model\TagFactory $tagFactory
    ) {
        $this->tagBuffer = $tagBuffer;
        $this->tagFactory = $tagFactory;
    }

    abstract public function process(): void;

    abstract public function generateResultMessage(): void;

    public function setStatusChanger(int $statusChanger): void
    {
        $this->statusChanger = $statusChanger;
    }

    protected function getStatusChanger(): int
    {
        return $this->statusChanger;
    }

    public function setParams(array $params = []): void
    {
        $this->params = $params;
    }

    protected function getParams(): array
    {
        return $this->params;
    }

    // ---------------------------------------

    public function setListingProduct(\M2E\Temu\Model\Product $product): void
    {
        $this->listingProduct = $product;
    }

    protected function getProduct(): \M2E\Temu\Model\Product
    {
        return $this->listingProduct;
    }

    // ---------------------------------------

    public function setConfigurator(\M2E\Temu\Model\Product\Action\Configurator $object): void
    {
        $this->configurator = $object;
    }

    protected function getConfigurator(): \M2E\Temu\Model\Product\Action\Configurator
    {
        return $this->configurator;
    }

    public function setVariantsSettings(\M2E\Temu\Model\Product\Action\VariantSettings $variantSettings): void
    {
        $this->variantSettings = $variantSettings;
    }

    protected function getVariantSettings(): \M2E\Temu\Model\Product\Action\VariantSettings
    {
        return $this->variantSettings;
    }

    // ---------------------------------------

    public function setResponseData(array $value): self
    {
        $this->responseData = $value;

        return $this;
    }

    protected function getResponseData(): array
    {
        return $this->responseData;
    }

    // ---------------------------------------

    public function setRequestMetaData(array $value): self
    {
        $this->requestMetaData = $value;

        return $this;
    }

    public function getRequestMetaData(): array
    {
        return $this->requestMetaData;
    }

    public function setLogBuffer($logBuffer): self
    {
        $this->logBuffer = $logBuffer;

        return $this;
    }

    public function getLogBuffer(): \M2E\Temu\Model\Product\Action\LogBuffer
    {
        return $this->logBuffer;
    }

    // ----------------------------------------

    protected function addTags($messages): void
    {
        $tags = [];
        foreach ($messages as $message) {
            $tags[] = $this->tagFactory->createByErrorCode((string)$message['code'], $message['text']);
        }

        if (!empty($tags)) {
            $tags[] = $this->tagFactory->createWithHasErrorCode();

            $this->tagBuffer->addTags($this->getProduct(), $tags);
            $this->tagBuffer->flush();
        }
    }
}
