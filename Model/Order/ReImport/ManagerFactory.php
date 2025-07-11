<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\ReImport;

class ManagerFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(\M2E\Temu\Model\Account $account): Manager
    {
        return $this->objectManager->create(Manager::class, ['account' => $account]);
    }
}
