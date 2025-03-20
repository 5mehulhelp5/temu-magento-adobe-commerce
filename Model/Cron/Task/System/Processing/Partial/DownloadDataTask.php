<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task\System\Processing\Partial;

class DownloadDataTask implements \M2E\Core\Model\Cron\TaskHandlerInterface
{
    public const NICK = 'processing/partial/download/data';

    private \M2E\Temu\Model\Processing\RetrieveData\Partial $retrieveDataPartial;

    public function __construct(
        \M2E\Temu\Model\Processing\RetrieveData\Partial $retrieveDataPartial
    ) {
        $this->retrieveDataPartial = $retrieveDataPartial;
    }

    protected function getNick(): string
    {
        return self::NICK;
    }

    public function process($context): void
    {
        $this->retrieveDataPartial->process();
    }
}
