<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action;

class ConfiguratorFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Configurator
    {
        return $this->objectManager->create(Configurator::class);
    }
}
