<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task\Order;

class UpdateShippingTask extends \M2E\Temu\Model\Cron\AbstractTask
{
    public const NICK = 'order/update_shipping';

    private \M2E\Temu\Model\Order\Change\Repository $orderChangeRepository;
    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Model\Order\Change\ShippingProcessor $shippingProcessor;

    public function __construct(
        \M2E\Temu\Model\Order\Change\ShippingProcessor $shippingProcessor,
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\Order\Change\Repository $orderChangeRepository,
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
            $resource
        );
        $this->orderChangeRepository = $orderChangeRepository;
        $this->accountRepository = $accountRepository;
        $this->shippingProcessor = $shippingProcessor;
    }

    protected function getNick(): string
    {
        return self::NICK;
    }

    protected function getSynchronizationLog(): \M2E\Temu\Model\Synchronization\LogService
    {
        $synchronizationLog = parent::getSynchronizationLog();
        $synchronizationLog->setTask(\M2E\Temu\Model\Synchronization\Log::TASK_ORDERS);

        return $synchronizationLog;
    }

    protected function performActions(): void
    {
        $this->deleteNotActualChanges();

        $accounts = $this->accountRepository->getAll();
        if (empty($accounts)) {
            return;
        }

        foreach ($accounts as $account) {
            $this->getOperationHistory()->addText('Starting Account "' . $account->getTitle() . '"');

            try {
                $this->shippingProcessor->process($account);
            } catch (\Throwable $exception) {
                $message = (string)__(
                    'The "Update" Action for Account "%1" was completed with error.',
                    $account->getTitle()
                );

                $this->processTaskAccountException($message, __FILE__, __LINE__);
                $this->processTaskException($exception);
            }
        }
    }

    // ----------------------------------------

    private function deleteNotActualChanges(): void
    {
        $this->orderChangeRepository->deleteByProcessingAttemptCount(
            \M2E\Temu\Model\Order\Change::MAX_ALLOWED_PROCESSING_ATTEMPTS
        );
    }
}
