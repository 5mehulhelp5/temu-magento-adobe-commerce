<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\DataProvider;

use M2E\Temu\Model\Product\DataProvider\DataBuilderInterface;
use M2E\Temu\Model\Product\DataProvider\FactoryInterface;

class Factory implements FactoryInterface
{
    private const ALLOWED_BUILDERS = [
        PriceProvider::NICK => PriceProvider::class,
        QtyProvider::NICK => QtyProvider::class,
    ];

    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(string $nick): DataBuilderInterface
    {
        if (!isset(self::ALLOWED_BUILDERS[$nick])) {
            throw new \M2E\Temu\Model\Exception\Logic(sprintf('Unknown builder - %s', $nick));
        }

        /** @var DataBuilderInterface */
        return $this->objectManager->create(self::ALLOWED_BUILDERS[$nick]);
    }
}
