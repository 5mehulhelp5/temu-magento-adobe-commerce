<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task\Order;

class SyncTask implements \M2E\Core\Model\Cron\TaskHandlerInterface
{
    public const NICK = 'order/sync';
    private Sync\ProcessorFactory $ordersProcessorFactory;
    private \M2E\Temu\Model\Account\Repository $accountRepository;

    public function __construct(
        \M2E\Temu\Model\Account\Repository $accountRepository,
        Sync\ProcessorFactory $ordersProcessorFactory
    ) {
        $this->ordersProcessorFactory = $ordersProcessorFactory;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param \M2E\Temu\Model\Cron\TaskContext $context
     *
     * @return void
     */
    public function process($context): void
    {
        $context->getSynchronizationLog()->setTask(\M2E\Temu\Model\Synchronization\Log::TASK_ORDERS);

        foreach ($this->accountRepository->getAll() as $account) {
            try {
                $ordersProcessor = $this->ordersProcessorFactory->create($account);
                $ordersProcessor->process();
            } catch (\Throwable $e) {
                $context->getExceptionHandler()->processTaskException($e);
            }
        }
    }
}
