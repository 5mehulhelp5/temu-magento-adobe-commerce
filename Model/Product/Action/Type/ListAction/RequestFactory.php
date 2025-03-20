<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\ListAction;

class RequestFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Request
    {
        return $this->objectManager->create(Request::class);
    }
}
