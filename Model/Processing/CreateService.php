<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Processing;

class CreateService
{
    private \M2E\Temu\Model\ProcessingFactory $processingFactory;
    /** @var \M2E\Temu\Model\Processing\Repository */
    private Repository $repository;

    public function __construct(
        \M2E\Temu\Model\ProcessingFactory $processingFactory,
        Repository $repository
    ) {
        $this->processingFactory = $processingFactory;
        $this->repository = $repository;
    }

    public function createSingle(
        string $serverHash,
        string $handlerNick,
        array $params,
        \DateTime $expireDate
    ): \M2E\Temu\Model\Processing {
        return $this->create(
            $serverHash,
            $handlerNick,
            $params,
            $expireDate,
            \M2E\Temu\Model\Processing::TYPE_SIMPLE,
        );
    }

    public function createPartial(
        string $serverHash,
        string $handlerNick,
        array $params,
        \DateTime $expireDate
    ): \M2E\Temu\Model\Processing {
        return $this->create(
            $serverHash,
            $handlerNick,
            $params,
            $expireDate,
            \M2E\Temu\Model\Processing::TYPE_PARTIAL,
        );
    }

    private function create(
        string $serverHash,
        string $handlerNick,
        array $params,
        \DateTime $expireDate,
        int $type
    ): \M2E\Temu\Model\Processing {
        $processing = $this->processingFactory->create($type, $serverHash, $handlerNick, $params, $expireDate);

        $this->repository->create($processing);

        return $processing;
    }
}
