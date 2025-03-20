<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\ListAction;

class ProcessEnd extends \M2E\Temu\Model\Product\Action\Async\AbstractProcessEnd
{
    private ResponseFactory $responseFactory;

    public function __construct(
        ResponseFactory $responseFactory
    ) {
        $this->responseFactory = $responseFactory;
    }

    protected function processComplete(array $resultData, array $messages): void
    {
        if (empty($resultData)) {
            return;
        }

        $this->processSuccess($resultData);
    }

    private function processSuccess(array $data): void
    {
        /** @var Response $responseObj */
        $responseObj = $this->responseFactory->create(
            $this->getListingProduct(),
            $this->getActionConfigurator(),
            $this->getVariantSettings(),
            $this->getLogBuffer(),
            $this->getParams(),
            $this->getStatusChanger(),
            $this->getRequestMetadata(),
            $data
        );

        $responseObj->process();
        $responseObj->generateResultMessage();
    }
}
