<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task;

class InstructionsProcessTask implements \M2E\Core\Model\Cron\TaskHandlerInterface
{
    public const NICK = 'instructions/process';

    private \M2E\Temu\Model\Instruction\Processor $instructionProcessor;

    public function __construct(
        \M2E\Temu\Model\Instruction\Processor $instructionProcessor
    ) {
        $this->instructionProcessor = $instructionProcessor;
    }

    public function process($context): void
    {
        $this->instructionProcessor->process();
    }
}
