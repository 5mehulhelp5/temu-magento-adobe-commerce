<?php

declare(strict_types=1);

namespace M2E\Temu\Model\UnmanagedProduct;

class Reset
{
    private DeleteService $deleteService;
    private \M2E\Temu\Model\Account\Repository $accountRepository;

    public function __construct(
        \M2E\Temu\Model\Account\Repository $accountRepository,
        DeleteService $deleteService
    ) {
        $this->deleteService = $deleteService;
        $this->accountRepository = $accountRepository;
    }

    public function process(\M2E\Temu\Model\Account $account): void
    {
        $this->deleteService->deleteUnmanagedByAccountId($account->getId());
        $this->resetAccountOtherListingsSynchronization($account);
    }

    private function resetAccountOtherListingsSynchronization(\M2E\Temu\Model\Account $account): void
    {
        $account->resetInventoryLastSyncDate();
        $this->accountRepository->save($account);
    }
}
