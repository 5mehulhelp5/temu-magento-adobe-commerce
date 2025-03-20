<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\ControlPanel\Cron;

class Run extends \M2E\Temu\Controller\Adminhtml\ControlPanel\AbstractMain
{
    private \M2E\Temu\Model\Cron\Runner\Developer $cronRunner;
    private \M2E\Temu\Model\Cron\TaskCollection $taskCollection;

    public function __construct(
        \M2E\Temu\Model\Cron\Runner\Developer $cronRunner,
        \M2E\Temu\Model\Cron\TaskCollection $taskCollection
    ) {
        parent::__construct();
        $this->taskCollection = $taskCollection;
        $this->cronRunner = $cronRunner;
    }

    public function execute(): void
    {
        $taskNick = $this->getRequest()->getParam('task_code');

        if (!empty($taskNick)) {
            $taskNicks = [$taskNick];
        } else {
            $taskNicks = array_map(
                static function (\M2E\Core\Model\Cron\TaskDefinition $definition) {
                    return $definition->getNick();
                },
                $this->taskCollection->getAllTasks()
            );
        }

        $this->cronRunner->setAllowedTasks($taskNicks);

        $this->cronRunner->process();

        $this->getResponse()->setBody('<pre>' . $this->cronRunner->getOperationHistory()->getFullDataInfo() . '</pre>');
    }
}
