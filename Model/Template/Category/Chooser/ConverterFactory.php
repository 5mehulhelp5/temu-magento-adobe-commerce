<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Template\Category\Chooser;

class ConverterFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Converter
    {
        return $this->objectManager->create(Converter::class);
    }
}
