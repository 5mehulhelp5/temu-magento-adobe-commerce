<?php

declare(strict_types=1);

namespace M2E\Temu\Model\StopQueue;

class Create
{
    private \M2E\Temu\Model\StopQueueFactory $stopQueueFactory;
    private Repository $repository;
    private \M2E\Temu\Helper\Module\Exception $helperException;
    private \M2E\Temu\Helper\Module\Logger $logger;

    public function __construct(
        \M2E\Temu\Model\StopQueueFactory $stopQueueFactory,
        \M2E\Temu\Model\StopQueue\Repository $repository,
        \M2E\Temu\Helper\Module\Exception $helperException,
        \M2E\Temu\Helper\Module\Logger $logger
    ) {
        $this->stopQueueFactory = $stopQueueFactory;
        $this->repository = $repository;
        $this->helperException = $helperException;
        $this->logger = $logger;
    }

    public function process(\M2E\Temu\Model\Product $product): void
    {
        if (!$product->isStoppable()) {
            return;
        }

        try {
            $stopQueue = $this->stopQueueFactory->create(
                $product->getAccount()->getId(),
                $product->getChannelProductId()
            );
            $this->repository->create($stopQueue);
        } catch (\Throwable $exception) {
            $sku = $product->getChannelProductId();

            $this->logger->process(
                sprintf(
                    'Product [Listing Product ID: %s] was not added to stop queue because of the error: %s',
                    $product->getId(),
                    $exception->getMessage()
                ),
                'Product was not added to stop queue'
            );

            $this->helperException->process($exception);
        }
    }
}
