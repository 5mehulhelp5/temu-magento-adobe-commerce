<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task\Product;

class InspectDirectChangesTask extends \M2E\Temu\Model\Cron\AbstractTask
{
    public const NICK = 'product/inspect_direct_changes';

    private \M2E\Temu\Model\Product\InspectDirectChanges $inspectDirectChanges;
    private \M2E\Temu\Model\Product\InspectDirectChanges\Config $config;

    public function __construct(
        \M2E\Temu\Model\Product\InspectDirectChanges\Config $config,
        \M2E\Temu\Model\Product\InspectDirectChanges $inspectDirectChanges,
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

        $this->config = $config;
        $this->inspectDirectChanges = $inspectDirectChanges;
    }

    protected function getNick(): string
    {
        return self::NICK;
    }

    public function isPossibleToRun()
    {
        if (
            !$this->config->isEnableProductInspectorMode()
        ) {
            return false;
        }

        return parent::isPossibleToRun();
    }

    protected function performActions(): void
    {
        $this->inspectDirectChanges->process();
    }
}
