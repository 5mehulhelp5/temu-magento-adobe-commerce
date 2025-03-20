<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task;

class InventorySyncTask implements \M2E\Core\Model\Cron\TaskHandlerInterface
{
    public const NICK = 'inventory/sync';

    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Model\InventorySync\InitializeFactory $syncInitiatorFactory;

    public function __construct(
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\InventorySync\InitializeFactory $syncInitiatorFactory
    ) {
        $this->accountRepository = $accountRepository;
        $this->syncInitiatorFactory = $syncInitiatorFactory;
    }

    /**
     * @param \M2E\Temu\Model\Cron\TaskContext $context
     *
     * @return void
     */
    public function process($context): void
    {
        $context->getSynchronizationLog()->setTask(\M2E\Temu\Model\Synchronization\Log::TASK_OTHER_LISTINGS);
        $context->getSynchronizationLog()->setInitiator(\M2E\Core\Helper\Data::INITIATOR_EXTENSION);

        // ----------------------------------------
        foreach ($this->accountRepository->findWithEnabledInventorySync() as $account) {
            try {
                $context->getOperationHistory()->addText(
                    "Starting Account '{$account->getTitle()} '",
                );

                $syncInitiator = $this->syncInitiatorFactory->create($account);
                if (!$syncInitiator->isAllowed()) {
                    $context->getOperationHistory()->addText(
                        "Skipped Account '{$account->getTitle()}'",
                    );

                    continue;
                }

                $context->getOperationHistory()->addTimePoint(
                    $account->getId(),
                    "Process Account '{$account->getTitle()} '",
                );

                // ----------------------------------------

                $syncInitiator->process();
            } catch (\Throwable $e) {
                $context->getOperationHistory()->addText(
                    "Error '{$account->getTitle()} '. Message: {$e->getMessage()}",
                );
            }

            // ----------------------------------------

            $context->getOperationHistory()->saveTimePoint($account->getId());
        }
    }
}
