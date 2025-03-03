<?php

declare(strict_types=1);

namespace M2E\Temu\Model\InventorySync\Processing;

class Initiator implements \M2E\Temu\Model\Processing\PartialInitiatorInterface
{
    private \M2E\Temu\Model\Account $account;

    public function __construct(
        \M2E\Temu\Model\Account $account
    ) {
        $this->account = $account;
    }

    public function getInitCommand(): \M2E\Temu\Model\Channel\Connector\Inventory\InventoryGetItemsCommand
    {
        return new \M2E\Temu\Model\Channel\Connector\Inventory\InventoryGetItemsCommand(
            $this->account->getServerHash()
        );
    }

    public function generateProcessParams(): array
    {
        return [
            'account_id' => $this->account->getId(),
            'current_date' => \M2E\Core\Helper\Date::createCurrentGmt()->format('Y-m-d H:i:s'),
        ];
    }

    public function getResultHandlerNick(): string
    {
        return ResultHandler::NICK;
    }

    public function initLock(\M2E\Temu\Model\Processing\LockManager $lockManager): void
    {
        $lockManager->create(\M2E\Temu\Model\Account::LOCK_NICK, $this->account->getId());
    }
}
