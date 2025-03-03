<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Account\Ui;

class RuntimeStorage
{
    private \M2E\Temu\Model\Account $account;

    public function hasAccount(): bool
    {
        return isset($this->account);
    }

    public function setAccount(\M2E\Temu\Model\Account $account): void
    {
        $this->account = $account;
    }

    public function getAccount(): \M2E\Temu\Model\Account
    {
        if (!$this->hasAccount()) {
            throw new \LogicException('Account was not initialized.');
        }

        return $this->account;
    }
}
