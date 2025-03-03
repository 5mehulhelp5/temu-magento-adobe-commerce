<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\Stop;

class Response extends \M2E\Temu\Model\Product\Action\Type\AbstractResponse
{
    private \M2E\Temu\Model\Product\Repository $productRepository;

    public function __construct(
        \M2E\Temu\Model\Product\Repository $productRepository,
        \M2E\Temu\Model\Tag\ListingProduct\Buffer $tagBuffer,
        \M2E\Temu\Model\TagFactory $tagFactory
    ) {
        parent::__construct($tagBuffer, $tagFactory);

        $this->productRepository = $productRepository;
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
        $this->getProduct()->setStatusInactive($this->getStatusChanger());

        foreach ($this->getProduct()->getVariants() as $variant) {
            if ($variant->isStatusListed()) {
                $variant->changeStatusToInactive();

                $this->productRepository->saveVariantSku($variant);
            }
        }

        $this->productRepository->save($this->getProduct());
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
            $this->getLogBuffer()->addFail('Product failed to be stopped.');

            return;
        }

        $this->getLogBuffer()->addSuccess('Item was Stopped');
    }
}
