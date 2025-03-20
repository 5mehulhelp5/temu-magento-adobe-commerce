<?php

declare(strict_types=1);

namespace M2E\Temu\Model\InventorySync;

class Initialize
{
    private const SYNC_INTERVAL_24_HOURS_IN_SECONDS = 86400;

    private \M2E\Temu\Model\Account $account;

    /** @var \M2E\Temu\Model\InventorySync\LockManager */
    private LockManager $lockManager;
    private \M2E\Temu\Model\Processing\Runner $processingRunner;
    /** @var \M2E\Temu\Model\InventorySync\Processing\InitiatorFactory */
    private Processing\InitiatorFactory $processingInitiatorFactory;
    /** @var \M2E\Temu\Model\InventorySync\ReceivedProduct\Processor */
    private ReceivedProduct\Processor $receivedProductProcessor;

    public function __construct(
        \M2E\Temu\Model\Account $account,
        \M2E\Temu\Model\InventorySync\LockManager $lockManager,
        \M2E\Temu\Model\Processing\Runner $processingRunner,
        \M2E\Temu\Model\InventorySync\Processing\InitiatorFactory $processingInitiatorFactory,
        \M2E\Temu\Model\InventorySync\ReceivedProduct\Processor $receivedProductProcessor
    ) {
        $this->account = $account;
        $this->lockManager = $lockManager;
        $this->processingRunner = $processingRunner;
        $this->processingInitiatorFactory = $processingInitiatorFactory;
        $this->receivedProductProcessor = $receivedProductProcessor;
    }

    public function isAllowed(): bool
    {
        $currentDate = \M2E\Core\Helper\Date::createCurrentGmt();

        $lastSyncDate = $this->account->getInventoryLastSyncDate();
        if (
            $lastSyncDate !== null
            && $lastSyncDate->modify('+ ' . self::SYNC_INTERVAL_24_HOURS_IN_SECONDS . ' seconds') > $currentDate
        ) {
            return false;
        }

        return !$this->lockManager->isExistByAccount($this->account);
    }

    public function process(): void
    {
        if (!$this->isAllowed()) {
            return;
        }

        $this->receivedProductProcessor->clear($this->account);

        $initiator = $this->processingInitiatorFactory->create($this->account);

        $this->processingRunner->run($initiator);
    }
}
