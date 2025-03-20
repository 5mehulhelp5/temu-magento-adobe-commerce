<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Update\y25_m03;

class RemoveOldCronValues extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $coreConfig = $this->getConfigModifier(\M2E\Temu\Helper\Module::IDENTIFIER);
        $coreConfig->delete('/cron/', 'last_executed_task_group');
    }
}
