<?php

namespace M2E\Temu\Model\Cron\Runner;

use M2E\Temu\Model\Cron\AbstractRunner;

class Developer extends AbstractRunner
{
    private array $allowedTasks;
    private \M2E\Temu\Model\Cron\TaskRepository $taskRepository;

    public function __construct(
        \M2E\Temu\Model\Cron\TaskRepository $taskRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \M2E\Temu\Model\Lock\Transactional\ManagerFactory $lockTransactionManagerFactory,
        \M2E\Temu\Helper\Module\Exception $exceptionHelper,
        \M2E\Core\Helper\Magento $magentoHelper,
        \M2E\Temu\Model\Config\Manager $config,
        \M2E\Temu\Helper\Module $moduleHelper,
        \M2E\Temu\Helper\Module\Maintenance $maintenanceHelper,
        \M2E\Temu\Helper\Module\Cron $cronHelper,
        \M2E\Temu\Model\Cron\OperationHistoryFactory $operationHistoryFactory,
        \M2E\Core\Helper\Client\MemoryLimit $memoryLimit,
        \M2E\Temu\Model\Cron\Strategy $strategy
    ) {
        parent::__construct(
            $storeManager,
            $lockTransactionManagerFactory,
            $exceptionHelper,
            $magentoHelper,
            $config,
            $moduleHelper,
            $maintenanceHelper,
            $cronHelper,
            $operationHistoryFactory,
            $memoryLimit,
            $strategy,
        );

        $this->taskRepository = $taskRepository;
    }

    public function getNick(): ?string
    {
        return null;
    }

    public function getInitiator(): int
    {
        return \M2E\Core\Helper\Data::INITIATOR_DEVELOPER;
    }

    public function process(): void
    {
        // @codingStandardsIgnoreLine
        session_write_close();
        parent::process();
    }

    protected function getStrategy(): \M2E\Temu\Model\Cron\Strategy
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->allowedTasks)) {
            $this->allowedTasks = $this->taskRepository->getRegisteredTasks();
        }

        $strategy = parent::getStrategy();
        $strategy->setAllowedTasks($this->allowedTasks);

        return $strategy;
    }

    /**
     * @param array $tasks
     *
     * @return $this
     */
    public function setAllowedTasks(array $tasks): self
    {
        $this->allowedTasks = $tasks;

        return $this;
    }

    protected function isPossibleToRun(): bool
    {
        return true;
    }

    protected function canProcessRunner(): bool
    {
        return true;
    }

    protected function setLastRun(): void
    {
    }

    protected function setLastAccess(): void
    {
    }
}
