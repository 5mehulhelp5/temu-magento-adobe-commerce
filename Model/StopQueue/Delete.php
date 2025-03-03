<?php

declare(strict_types=1);

namespace M2E\Temu\Model\StopQueue;

class Delete
{
    private const MAXIMUM_PRODUCTS_PER_REQUEST = 200;

    private \M2E\Temu\Model\StopQueue\Repository $repository;

    private \M2E\Temu\Model\Connector\Client\Single $serverClient;

    public function __construct(
        \M2E\Temu\Model\StopQueue\Repository $repository,
        \M2E\Temu\Model\Connector\Client\Single $serverClient
    ) {
        $this->repository = $repository;
        $this->serverClient = $serverClient;
    }

    public function process(): void
    {
        foreach ($this->repository->getAccounts() as $row) {
            $productsToDelete = $this->repository->getChannelProductIdsByAccount(
                (int)$row['account_id'],
                self::MAXIMUM_PRODUCTS_PER_REQUEST
            );

            if (!empty($productsToDelete)) {
                $command = new \M2E\Temu\Model\Channel\Connector\Product\BulkStopCommand(
                    $row['server_hash'],
                    $productsToDelete
                );

                $response = $this->serverClient->process($command);

                if ($response->isResultSuccess() && empty($response->getResponseData())) {
                    $this->repository->massStatusUpdate(
                        $productsToDelete,
                        (int)$row['account_id']
                    );
                }
            }
        }
    }

    public function clearOld(\DateTime $borderDate): void
    {
        $this->repository->deleteCompletedAfterBorderDate($borderDate);
    }
}
