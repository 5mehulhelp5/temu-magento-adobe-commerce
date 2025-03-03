<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task\Order;

class SyncTask extends \M2E\Temu\Model\Cron\AbstractTask
{
    public const NICK = 'order/sync';

    /** @var int in seconds */
    protected int $intervalInSeconds = 300;

    private Sync\ProcessorFactory $ordersProcessorFactory;
    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Helper\Module\Exception $exceptionHelper;

    public function __construct(
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Helper\Module\Exception $exceptionHelper,
        \M2E\Temu\Model\Cron\Manager $cronManager,
        Sync\ProcessorFactory $ordersProcessorFactory,
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
        $this->ordersProcessorFactory = $ordersProcessorFactory;
        $this->accountRepository = $accountRepository;
        $this->exceptionHelper = $exceptionHelper;
    }

    protected function getNick(): string
    {
        return self::NICK;
    }

    protected function performActions(): void
    {
        $synchronizationLog = $this->getSynchronizationLog();
        $synchronizationLog->setTask(\M2E\Temu\Model\Synchronization\Log::TASK_ORDERS);

        foreach ($this->accountRepository->getAll() as $account) {
            try {
                $ordersProcessor = $this->ordersProcessorFactory->create($account);
                $ordersProcessor->process();
            } catch (\Throwable $e) {
                $this->exceptionHelper->process($e);
                $synchronizationLog->addFromException($e);
            }
        }
    }
}
