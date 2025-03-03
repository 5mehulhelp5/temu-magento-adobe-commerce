<?php

namespace M2E\Temu\Model\Cron;

abstract class AbstractTask
{
    protected int $initiator = \M2E\Core\Helper\Data::INITIATOR_UNKNOWN;
    protected int $intervalInSeconds = 60;

    private \M2E\Temu\Model\Synchronization\LogService $syncLogger;
    private \M2E\Temu\Model\Cron\Manager $cronManager;
    protected \Magento\Framework\Event\Manager $eventManager;
    protected \M2E\Temu\Model\ActiveRecord\Factory $activeRecordFactory;
    protected \Magento\Framework\App\ResourceConnection $resource;
    protected \M2E\Temu\Model\Lock\Item\Manager $lockItemManager;
    protected \M2E\Temu\Model\Cron\OperationHistory $operationHistory;
    protected \M2E\Temu\Model\Cron\OperationHistory $parentOperationHistory;
    protected \M2E\Temu\Model\Cron\TaskRepository $taskRepo;
    protected \M2E\Temu\Helper\Data $helperData;

    public function __construct(
        \M2E\Temu\Model\Cron\Manager $cronManager,
        \M2E\Temu\Model\Synchronization\LogService $syncLogger,
        \M2E\Temu\Helper\Data $helperData,
        \Magento\Framework\Event\Manager $eventManager,
        \M2E\Temu\Model\ActiveRecord\Factory $activeRecordFactory,
        \M2E\Temu\Model\Cron\TaskRepository $taskRepo,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->cronManager = $cronManager;
        $this->eventManager = $eventManager;
        $this->activeRecordFactory = $activeRecordFactory;
        $this->resource = $resource;
        $this->helperData = $helperData;
        $this->taskRepo = $taskRepo;
        $this->syncLogger = $syncLogger;
    }

    public function process(): void
    {
        $this->initialize();
        $this->cronManager->setLastAccess($this->getConfigGroup());

        if (!$this->isPossibleToRun()) {
            return;
        }

        $this->cronManager->setLastRun($this->getConfigGroup());
        $this->beforeStart();

        try {
            $this->eventManager->dispatch(
                \M2E\Temu\Model\Cron\Strategy::PROGRESS_START_EVENT_NAME,
                ['progress_nick' => $this->getNick()]
            );

            $this->performActions();

            $this->eventManager->dispatch(
                \M2E\Temu\Model\Cron\Strategy::PROGRESS_STOP_EVENT_NAME,
                ['progress_nick' => $this->getNick()]
            );
        } catch (\Throwable $exception) {
            $this->processTaskException($exception);
        }

        $this->afterEnd();
    }

    // ---------------------------------------

    abstract protected function performActions();

    abstract protected function getNick(): string;

    // ---------------------------------------

    public function setInitiator(int $value): void
    {
        $this->initiator = $value;
    }

    public function getInitiator(): int
    {
        return $this->initiator;
    }

    // ---------------------------------------

    /**
     * @param \M2E\Temu\Model\Lock\Item\Manager $lockItemManager
     *
     * @return $this
     */
    public function setLockItemManager(\M2E\Temu\Model\Lock\Item\Manager $lockItemManager)
    {
        $this->lockItemManager = $lockItemManager;

        return $this;
    }

    /**
     * @return \M2E\Temu\Model\Lock\Item\Manager
     */
    public function getLockItemManager()
    {
        return $this->lockItemManager;
    }

    // ---------------------------------------

    /**
     * @param \M2E\Temu\Model\Cron\OperationHistory $object
     *
     * @return $this
     */
    public function setParentOperationHistory(\M2E\Temu\Model\Cron\OperationHistory $object)
    {
        $this->parentOperationHistory = $object;

        return $this;
    }

    /**
     * @return \M2E\Temu\Model\Cron\OperationHistory
     */
    public function getParentOperationHistory()
    {
        return $this->parentOperationHistory;
    }

    // ---------------------------------------

    protected function getSynchronizationLog(): \M2E\Temu\Model\Synchronization\LogService
    {
        $this->syncLogger->setInitiator($this->getInitiator());

        return $this->syncLogger;
    }

    /**
     * @return bool
     */
    public function isPossibleToRun()
    {
        if ($this->getInitiator() === \M2E\Core\Helper\Data::INITIATOR_DEVELOPER) {
            return true;
        }

        if (!$this->isModeEnabled()) {
            return false;
        }

        $currentTimeStamp = \M2E\Core\Helper\Date::createCurrentGmt()->getTimestamp();

        $startFrom = $this->getConfigValue('start_from');
        $startFrom = !empty($startFrom) ?
            (int)\M2E\Core\Helper\Date::createDateGmt($startFrom)->format('U') : $currentTimeStamp;

        return $startFrom <= $currentTimeStamp && $this->isIntervalExceeded();
    }

    private function initialize(): void
    {
        /** @var \M2E\Temu\Helper\Module\Exception $exceptionHelper */
        $exceptionHelper = \Magento\Framework\App\ObjectManager::getInstance()
                                                               ->get(\M2E\Temu\Helper\Module\Exception::class);
        $exceptionHelper->setFatalErrorHandler();
        $this->getSynchronizationLog()->registerFatalErrorHandler();
    }

    // ---------------------------------------

    protected function beforeStart(): void
    {
        $parentId = $this->getParentOperationHistory()
            ? $this->getParentOperationHistory()->getObject()->getId() : null;
        $nick = str_replace("/", "_", $this->getNick());
        $this->getOperationHistory()->start('cron_task_' . $nick, $parentId, $this->getInitiator());
        $this->getOperationHistory()->makeShutdownFunction();

        $this->getSynchronizationLog()->setOperationHistoryId($this->getOperationHistory()->getObject()->getId());
    }

    protected function afterEnd(): void
    {
        $this->getOperationHistory()->stop();
    }

    protected function getOperationHistory(): \M2E\Temu\Model\Cron\OperationHistory
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->operationHistory)) {
            return $this->operationHistory;
        }

        return $this->operationHistory = $this->activeRecordFactory->getObject('Cron_OperationHistory');
    }

    // ---------------------------------------

    protected function isModeEnabled(): bool
    {
        $mode = $this->getConfigValue('mode');

        if ($mode !== null) {
            return (bool)$mode;
        }

        return true;
    }

    protected function isIntervalExceeded(): bool
    {
        $lastRun = $this->cronManager->getLastRun($this->getConfigGroup());

        if ($lastRun === null) {
            return true;
        }

        $currentTimeStamp = \M2E\Core\Helper\Date::createCurrentGmt()->getTimestamp();
        $lastRunTimestamp = (int)$lastRun->format('U');

        return $currentTimeStamp > $lastRunTimestamp + $this->getIntervalInSeconds();
    }

    public function getIntervalInSeconds()
    {
        $interval = $this->getConfigValue('interval');

        return $interval === null ? $this->intervalInSeconds : (int)$interval;
    }

    protected function processTaskException(\Throwable $exception)
    {
        $this->getOperationHistory()->addContentData(
            'exceptions',
            [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]
        );

        $this->getSynchronizationLog()->addFromException($exception);

        /** @var \M2E\Temu\Helper\Module\Exception $exceptionHelper */
        $exceptionHelper = \Magento\Framework\App\ObjectManager::getInstance()
                                            ->get(\M2E\Temu\Helper\Module\Exception::class);
        $exceptionHelper->process($exception);
    }

    protected function processTaskAccountException($message, $file, $line, $trace = null)
    {
        $this->getOperationHistory()->addContentData(
            'exceptions',
            [
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'trace' => $trace,
            ]
        );

        $this->getSynchronizationLog()->add(
            $message,
            \M2E\Temu\Model\Log\AbstractModel::TYPE_ERROR
        );
    }

    /**
     * @return \M2E\Temu\Model\Config\Manager
     */
    protected function getConfig(): \M2E\Temu\Model\Config\Manager
    {
        /** @var \M2E\Temu\Model\Config\Manager */
        return \Magento\Framework\App\ObjectManager::getInstance()->get(\M2E\Temu\Model\Config\Manager::class);
    }

    protected function getConfigGroup(): string
    {
        return '/cron/task/' . $this->getNick() . '/';
    }

    // ---------------------------------------

    protected function setConfigValue($key, $value)
    {
        $this->getConfig()->set($this->getConfigGroup(), $key, $value);
    }

    protected function getConfigValue($key)
    {
        return $this->getConfig()->get($this->getConfigGroup(), $key);
    }
}
