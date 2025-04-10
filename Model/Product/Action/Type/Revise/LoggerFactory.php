<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\Revise;

class LoggerFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Logger
    {
        return $this->objectManager->create(Logger::class);
    }
}
