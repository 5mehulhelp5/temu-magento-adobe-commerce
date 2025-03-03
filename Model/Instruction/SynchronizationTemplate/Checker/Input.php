<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Instruction\SynchronizationTemplate\Checker;

class Input extends \M2E\Temu\Model\Instruction\Handler\Input
{
    private \M2E\Temu\Model\ScheduledAction $scheduledAction;

    public function setScheduledAction(\M2E\Temu\Model\ScheduledAction $scheduledAction): self
    {
        $this->scheduledAction = $scheduledAction;

        return $this;
    }

    public function getScheduledAction(): ?\M2E\Temu\Model\ScheduledAction
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        return $this->scheduledAction ?? null;
    }
}
