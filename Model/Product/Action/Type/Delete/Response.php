<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\Delete;

class Response extends \M2E\Temu\Model\Product\Action\Type\AbstractResponse
{
    private \M2E\Temu\Model\Product\RemoveHandler $removeHandler;

    public function __construct(
        \M2E\Temu\Model\Product\RemoveHandler $removeHandler,
        \M2E\Temu\Model\Tag\ListingProduct\Buffer $tagBuffer,
        \M2E\Temu\Model\TagFactory $tagFactory
    ) {
        parent::__construct($tagBuffer, $tagFactory);

        $this->removeHandler = $removeHandler;
    }

    public function process(): void
    {
        if (!$this->isSuccess()) {
            $this->processFail();

            return;
        }

        $this->processSuccess();
    }

    private function isSuccess(): bool
    {
        $responseData = $this->getResponseData();

        return $responseData['status'] === true;
    }

    public function processSuccess(): void
    {
        $listingProduct = $this->getProduct();

        $this->removeHandler->process(
            $listingProduct,
            \M2E\Core\Helper\Data::INITIATOR_USER
        );
    }

    public function processFail()
    {
        $responseData = $this->getResponseData();
        foreach ($responseData['messages'] as $message) {
            $this->getLogBuffer()->addFail($message['text']);
        }
    }

    public function generateResultMessage(): void
    {
        if (!$this->isSuccess()) {
            $this->getLogBuffer()->addFail('Product failed to be deleted.');

            return;
        }

        $this->getLogBuffer()->addSuccess('Item was removed');
    }
}
