<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task\Order\Sync;

class ProcessorFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(
        \M2E\Temu\Model\Account $account
    ): Processor {
        return $this->objectManager->create(
            Processor::class,
            ['account' => $account],
        );
    }
}
