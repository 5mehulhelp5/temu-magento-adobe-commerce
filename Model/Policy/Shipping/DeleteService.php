<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Policy\Shipping;

class DeleteService extends \M2E\Temu\Model\Policy\AbstractDeleteService
{
    private \M2E\Temu\Model\Policy\Shipping\Repository $shippingRepository;
    private \M2E\Temu\Model\Listing\Repository $listingRepository;

    public function __construct(
        \M2E\Temu\Model\Policy\Shipping\Repository $shippingRepository,
        \M2E\Temu\Model\Listing\Repository $listingRepository
    ) {
        $this->shippingRepository = $shippingRepository;
        $this->listingRepository = $listingRepository;
    }

    protected function loadPolicy(int $id): \M2E\Temu\Model\Policy\PolicyInterface
    {
        return $this->shippingRepository->get($id);
    }

    protected function isUsedPolicy(\M2E\Temu\Model\Policy\PolicyInterface $policy): bool
    {
        return $this->listingRepository->isExistListingByShippingPolicy($policy->getId());
    }

    protected function delete(\M2E\Temu\Model\Policy\PolicyInterface $policy): void
    {
        /** @var \M2E\Temu\Model\Policy\Shipping $policy */
        $this->shippingRepository->delete($policy);
    }
}
