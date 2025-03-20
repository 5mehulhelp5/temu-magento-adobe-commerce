<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Policy;

class ShippingFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createEmpty(): Shipping
    {
        return $this->objectManager->create(Shipping::class);
    }

    public function create(
        \M2E\Temu\Model\Account $account,
        string $title,
        string $shippingTemplateId,
        int $preparationTime
    ): Shipping {
        $model = $this->createEmpty();
        $model->create(
            $account->getId(),
            $title,
            $shippingTemplateId,
            $preparationTime
        );

        return $model;
    }
}
