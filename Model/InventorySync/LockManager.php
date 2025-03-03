<?php

declare(strict_types=1);

namespace M2E\Temu\Model\InventorySync;

class LockManager
{
    private \M2E\Temu\Model\Processing\Lock\Repository $processingLockRepository;

    public function __construct(
        \M2E\Temu\Model\Processing\Lock\Repository $processingLockRepository
    ) {
        $this->processingLockRepository = $processingLockRepository;
    }

    public function isExistByAccount(\M2E\Temu\Model\Account $account): bool
    {
        return $this->processingLockRepository->isExist(\M2E\Temu\Model\Account::LOCK_NICK, $account->getId());
    }
}
