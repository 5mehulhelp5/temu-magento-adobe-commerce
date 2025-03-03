<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action;

class ProcessorAsyncFactory
{
    private LoggerFactory $loggerFactory;
    private \M2E\Temu\Model\Product\Action\Async\Factory $asyncActionFactory;
    private \M2E\Temu\Model\Product\LockManager $lockManager;
    private \M2E\Temu\Model\Processing\Runner $processingRunner;
    private \M2E\Temu\Model\Product\Action\Async\Processing\InitiatorFactory $initiatorFactory;
    private \M2E\Temu\Model\Product\Action\LogBufferFactory $logBufferFactory;

    public function __construct(
        LoggerFactory $loggerFactory,
        \M2E\Temu\Model\Product\Action\Async\Factory $asyncActionFactory,
        \M2E\Temu\Model\Product\LockManager $lockManager,
        \M2E\Temu\Model\Processing\Runner $processingRunner,
        \M2E\Temu\Model\Product\Action\Async\Processing\InitiatorFactory $initiatorFactory,
        \M2E\Temu\Model\Product\Action\LogBufferFactory $logBufferFactory
    ) {
        $this->loggerFactory = $loggerFactory;
        $this->asyncActionFactory = $asyncActionFactory;
        $this->lockManager = $lockManager;
        $this->processingRunner = $processingRunner;
        $this->initiatorFactory = $initiatorFactory;
        $this->logBufferFactory = $logBufferFactory;
    }

    public function createProcessStart(
        string $nick,
        \M2E\Temu\Model\Product $listingProduct,
        Configurator $configurator,
        VariantSettings $variantSettings,
        int $statusChanger,
        int $actionLogId,
        int $logAction,
        array $params
    ): Async\AbstractProcessStart {
        $actionLogger = $this->loggerFactory->create(
            $actionLogId,
            $logAction,
            $this->getInitiatorByChanger($statusChanger),
        );

        $action = $this->asyncActionFactory->createActionStart($nick);
        $action->initialize(
            $actionLogger,
            $this->lockManager,
            $listingProduct,
            $configurator,
            $variantSettings,
            $this->processingRunner,
            $this->initiatorFactory,
            $this->logBufferFactory->create(),
            $params,
            $statusChanger
        );

        return $action;
    }

    public function createProcessEnd(
        string $nick,
        \M2E\Temu\Model\Product $listingProduct,
        Configurator $configurator,
        VariantSettings $variantSettings,
        int $initiator,
        int $actionLogId,
        int $actionLog,
        array $params,
        array $requestMetadata,
        int $statusChanger,
        array $warningMessages
    ): Async\AbstractProcessEnd {
        $actionLogger = $this->loggerFactory->create(
            $actionLogId,
            $actionLog,
            $initiator
        );

        $action = $this->asyncActionFactory->createActionEnd($nick);
        $action->initialize(
            $actionLogger,
            $this->lockManager,
            $listingProduct,
            $configurator,
            $variantSettings,
            $this->logBufferFactory->create(),
            $params,
            $requestMetadata,
            $statusChanger,
            $warningMessages
        );

        return $action;
    }

    // ----------------------------------------

    private function getInitiatorByChanger(int $statusChanger): int
    {
        switch ($statusChanger) {
            case \M2E\Temu\Model\Product::STATUS_CHANGER_UNKNOWN:
                return \M2E\Core\Helper\Data::INITIATOR_UNKNOWN;
            case \M2E\Temu\Model\Product::STATUS_CHANGER_USER:
                return \M2E\Core\Helper\Data::INITIATOR_USER;
            default:
                return \M2E\Core\Helper\Data::INITIATOR_EXTENSION;
        }
    }
}
