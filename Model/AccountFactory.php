<?php

declare(strict_types=1);

namespace M2E\Temu\Model;

class AccountFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function createEmpty(): Account
    {
        return $this->objectManager->create(Account::class);
    }

    public function create(
        string $title,
        string $identifier,
        string $serverHash,
        int $siteId,
        string $siteTitle,
        \M2E\Temu\Model\Account\Settings\UnmanagedListings $unmanagedListingsSettings,
        \M2E\Temu\Model\Account\Settings\Order $orderSettings,
        \M2E\Temu\Model\Account\Settings\InvoicesAndShipment $invoicesAndShipmentSettings
    ): Account {
        $model = $this->createEmpty();
        $model->create(
            $title,
            $identifier,
            $serverHash,
            $siteId,
            $siteTitle,
            $unmanagedListingsSettings,
            $orderSettings,
            $invoicesAndShipmentSettings
        );

        return $model;
    }
}
