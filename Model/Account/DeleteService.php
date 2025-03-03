<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Account;

use M2E\Temu\Model\Account\Issue\ValidTokens;

class DeleteService
{
    private Repository $accountRepository;
    private \M2E\Temu\Model\Order\Log\Repository $orderLogRepository;
    private \M2E\Temu\Model\Listing\Log\Repository $listingLogRepository;
    private \M2E\Temu\Helper\Module\Exception $exceptionHelper;
    private \M2E\Temu\Helper\Data\Cache\Permanent $cache;
    private \M2E\Temu\Model\UnmanagedProduct\DeleteService $unmanagedProductDeleteService;
    private \M2E\Temu\Model\Listing\DeleteService $listingDeleteService;
    private \M2E\Temu\Model\Listing\Repository $listingRepository;
    private \M2E\Temu\Model\Processing\DeleteService $processingDeleteService;
    private \M2E\Temu\Model\Order\DeleteService $deleteService;

    public function __construct(
        Repository $accountRepository,
        \M2E\Temu\Model\Listing\DeleteService $listingDeleteService,
        \M2E\Temu\Model\Order\Log\Repository $orderLogRepository,
        \M2E\Temu\Helper\Module\Exception $exceptionHelper,
        \M2E\Temu\Model\Listing\Log\Repository $listingLogRepository,
        \M2E\Temu\Model\UnmanagedProduct\DeleteService $unmanagedProductDeleteService,
        \M2E\Temu\Model\Listing\Repository $listingRepository,
        \M2E\Temu\Model\Processing\DeleteService $processingDeleteService,
        \M2E\Temu\Helper\Data\Cache\Permanent $cache,
        \M2E\Temu\Model\Order\DeleteService $deleteService
    ) {
        $this->accountRepository = $accountRepository;
        $this->orderLogRepository = $orderLogRepository;
        $this->listingLogRepository = $listingLogRepository;
        $this->exceptionHelper = $exceptionHelper;
        $this->cache = $cache;
        $this->unmanagedProductDeleteService = $unmanagedProductDeleteService;
        $this->listingDeleteService = $listingDeleteService;
        $this->listingRepository = $listingRepository;
        $this->processingDeleteService = $processingDeleteService;
        $this->deleteService = $deleteService;
    }

    /**
     * @param \M2E\Temu\Model\Account $account
     *
     * @return void
     * @throws \Throwable
     */
    public function delete(\M2E\Temu\Model\Account $account): void
    {
        $accountId = $account->getId();

        // ---------------------------------------

        try {
            $this->orderLogRepository->removeByAccountId($accountId);

            $this->deleteService->deleteByAccountId($accountId);

            $this->listingLogRepository->removeByAccountId($accountId);

            $this->unmanagedProductDeleteService->deleteUnmanagedByAccountId($accountId);

            $this->removeListings($account);

            $this->deleteAccount($account);
        } catch (\Throwable $e) {
            $this->exceptionHelper->process($e);
            throw $e;
        }
    }

    private function removeListings(\M2E\Temu\Model\Account $account): void
    {
        foreach ($this->listingRepository->findForAccount($account) as $listing) {
            $this->listingDeleteService->process($listing);
        }
    }

    private function deleteAccount(\M2E\Temu\Model\Account $account): void
    {
        $this->cache->removeTagValues('account');

        $this->accountRepository->remove($account);

        $this->cache->removeValue(ValidTokens::ACCOUNT_TOKENS_CACHE_KEY);
    }
}
