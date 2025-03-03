<?php

namespace M2E\Temu\Model\Cron\Task\Order;

class ReserveCancelTask extends \M2E\Temu\Model\Cron\AbstractTask
{
    public const NICK = 'order/reserve_cancel';

    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Model\Order\ReserveCancelProcessor $reserveCancelProcessor;

    public function __construct(
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\Order\ReserveCancelProcessor $reserveCancelProcessor,
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
        $this->accountRepository = $accountRepository;
        $this->reserveCancelProcessor = $reserveCancelProcessor;
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
        $permittedAccounts = $this->accountRepository->getAll();

        if (empty($permittedAccounts)) {
            return;
        }

        $this->getSynchronizationLog()->setInitiator(\M2E\Core\Helper\Data::INITIATOR_EXTENSION);

        foreach ($permittedAccounts as $account) {
            $this->getOperationHistory()->addText('Starting Account "' . $account->getTitle() . '"');

            try {
                $this->reserveCancelProcessor->process($account);
            } catch (\Exception $exception) {
                $message = (string)__(
                    'The "Reserve Cancellation" Action for Account "%1" was completed with error.',
                    $account->getTitle()
                );

                $this->processTaskAccountException($message, __FILE__, __LINE__);
                $this->processTaskException($exception);
            }
        }
    }
}
