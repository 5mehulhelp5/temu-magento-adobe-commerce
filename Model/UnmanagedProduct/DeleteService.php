<?php

declare(strict_types=1);

namespace M2E\Temu\Model\UnmanagedProduct;

class DeleteService
{
    /** @var \M2E\Temu\Model\UnmanagedProduct\Repository */
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function process(\M2E\Temu\Model\UnmanagedProduct $unmanagedProduct): void
    {
        foreach ($unmanagedProduct->getVariants() as $variant) {
            $this->repository->deleteVariant($variant);
        }

        $this->repository->delete($unmanagedProduct);
    }

    public function deleteUnmanagedByAccountId(int $accountId): void
    {
        $this->repository->removeVariantsByAccountId($accountId);
        $this->repository->removeProductByAccount($accountId);
    }
}
