<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task;

class ProcessScheduledActionsTask extends \M2E\Temu\Model\Cron\AbstractTask
{
    public const NICK = 'scheduled_actions/process';

    private \M2E\Temu\Model\ScheduledAction\Processor $processor;

    public function __construct(
        \M2E\Temu\Model\Cron\Manager $cronManager,
        \M2E\Temu\Model\Synchronization\LogService $syncLogger,
        \M2E\Temu\Model\ScheduledAction\Processor $processor,
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

        $this->processor = $processor;
    }

    protected function getNick(): string
    {
        return self::NICK;
    }

    protected function performActions(): void
    {
        $this->processor->process();
    }
}
