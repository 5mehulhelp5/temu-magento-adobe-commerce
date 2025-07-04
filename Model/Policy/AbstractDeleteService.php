<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Policy;

abstract class AbstractDeleteService
{
    public function process(int $id): void
    {
        try {
            $policy = $this->loadPolicy($id);
        } catch (\M2E\Temu\Model\Exception\Logic $exception) {
            throw new \M2E\Temu\Model\Exception\Logic((string)__($exception->getMessage()));
        }

        if ($this->isUsedPolicy($policy)) {
            throw new \M2E\Temu\Model\Exception\Logic(
                (string)__('Policy cannot be deleted as it is used in Listing Settings.')
            );
        }

        $this->delete($policy);
    }

    abstract protected function loadPolicy(int $id): \M2E\Temu\Model\Policy\PolicyInterface;

    abstract protected function isUsedPolicy(\M2E\Temu\Model\Policy\PolicyInterface $policy): bool;

    abstract protected function delete(\M2E\Temu\Model\Policy\PolicyInterface $policy): void;
}
