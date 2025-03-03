<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task;

class InventorySyncTask extends \M2E\Temu\Model\Cron\AbstractTask
{
    public const NICK = 'inventory/sync';

    protected int $intervalInSeconds = 300;

    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Model\InventorySync\InitializeFactory $syncInitiatorFactory;

    public function __construct(
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\InventorySync\InitializeFactory $syncInitiatorFactory,
        \M2E\Temu\Model\Cron\Manager $cronManager,
        \M2E\Temu\Model\Synchronization\LogService $syncLogger,
        \M2E\Temu\Helper\Data $helperData,
        \Magento\Framework\Event\Manager $eventManager,
        \M2E\Temu\Model\ActiveRecord\Factory $activeRecordFactory,
        \M2E\Temu\Model\Cron\TaskRepository $taskRepo,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        parent::__construct(
            $cronManager,
            $syncLogger,
            $helperData,
            $eventManager,
            $activeRecordFactory,
            $taskRepo,
            $resource,
        );
        $this->accountRepository = $accountRepository;
        $this->syncInitiatorFactory = $syncInitiatorFactory;
    }

    protected function getNick(): string
    {
        return self::NICK;
    }

    protected function getSynchronizationLog(): \M2E\Temu\Model\Synchronization\LogService
    {
        $synchronizationLog = parent::getSynchronizationLog();

        $synchronizationLog->setTask(\M2E\Temu\Model\Synchronization\Log::TASK_OTHER_LISTINGS);
        $synchronizationLog->setInitiator(\M2E\Core\Helper\Data::INITIATOR_EXTENSION);

        return $synchronizationLog;
    }

    protected function performActions(): void
    {
        foreach ($this->accountRepository->findWithEnabledInventorySync() as $account) {
            try {
                $this->getOperationHistory()->addText(
                    "Starting Account '{$account->getTitle()} '",
                );

                $syncInitiator = $this->syncInitiatorFactory->create($account);
                if (!$syncInitiator->isAllowed()) {
                    $this->getOperationHistory()->addText(
                        "Skipped Account '{$account->getTitle()}'",
                    );

                    continue;
                }

                $this->getOperationHistory()->addTimePoint(
                    $account->getId(),
                    "Process Account '{$account->getTitle()} '",
                );

                // ----------------------------------------

                $syncInitiator->process();
            } catch (\Throwable $e) {
                $this->getOperationHistory()->addText(
                    "Error '{$account->getTitle()} '. Message: {$e->getMessage()}",
                );
            }

            // ----------------------------------------

            $this->getOperationHistory()->saveTimePoint($account->getId());
        }
    }
}
