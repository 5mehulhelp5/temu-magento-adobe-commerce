<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\Relist;

class Response extends \M2E\Temu\Model\Product\Action\Type\Revise\Response
{
    public function process(): void
    {
        $response = $this->getResponseData();

        if (!$this->isSuccess()) {
            $this->addTags($response['messages']);

            return;
        }

        parent::process();
    }

    private function isSuccess(): bool
    {
        $responseData = $this->getResponseData();

        return $responseData['status'] === true;
    }

    protected function processSuccess(): void
    {
        $product = $this->getProduct();
        $product->setStatus(\M2E\Temu\Model\Product::STATUS_LISTED, $this->getStatusChanger());

        parent::processSuccess();
    }

    /**
     * @throws \Magento\Framework\Currency\Exception\CurrencyException
     */
    public function generateResultMessage(): void
    {
        if (!$this->isSuccess()) {
            $responseData = $this->getResponseData();
            if (empty($responseData['messages'])) {
                $this->getLogBuffer()->addFail('Product failed to be relisted.');

                return;
            }

            $resultMessage = sprintf(
                'Product failed to be relisted. Reason: %s',
                $responseData['messages'][0]['text']
            );

            $this->getLogBuffer()->addFail($resultMessage);

            return;
        }

        $message = 'Product was Relisted';

        $this->getLogBuffer()->addSuccess($message);
    }
}
