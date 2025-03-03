<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task\System\Servicing;

class SynchronizeTask extends \M2E\Temu\Model\Cron\AbstractTask
{
    public const NICK = 'system/servicing/synchronize';

    private \M2E\Temu\Model\Servicing\Dispatcher $dispatcher;

    public function __construct(
        \M2E\Temu\Model\Servicing\Dispatcher $dispatcher,
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
        $this->dispatcher = $dispatcher;
    }

    protected function getNick(): string
    {
        return self::NICK;
    }

    protected function performActions(): void
    {
        $this->dispatcher->process();
    }
}
