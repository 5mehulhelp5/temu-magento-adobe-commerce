<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Magento\Product\Rule\Custom;

class CustomFilterFactory
{
    private array $customFiltersMap = [
        Magento\Qty::NICK => Magento\Qty::class,
        Magento\Stock::NICK => Magento\Stock::class,
        Magento\TypeId::NICK => Magento\TypeId::class,
        Temu\ProductId::NICK => Temu\ProductId::class,
        Temu\OnlineCategory::NICK => Temu\OnlineCategory::class,
        Temu\OnlineTitle::NICK => Temu\OnlineTitle::class,
        Temu\OnlineQty::NICK => Temu\OnlineQty::class,
        Temu\OnlineSku::NICK => Temu\OnlineSku::class,
        Temu\OnlinePrice::NICK => Temu\OnlinePrice::class,
        Temu\Status::NICK => Temu\Status::class,
    ];

    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createByType(string $type): \M2E\Temu\Model\Magento\Product\Rule\Custom\AbstractCustomFilter
    {
        $filterClass = $this->choiceCustomFilterClass($type);
        if ($filterClass === null) {
            throw new \M2E\Temu\Model\Exception\Logic(
                sprintf('Unknown custom filter - %s', $type)
            );
        }

        return $this->objectManager->create($filterClass);
    }

    private function choiceCustomFilterClass(string $type): ?string
    {
        return $this->customFiltersMap[$type] ?? null;
    }
}
