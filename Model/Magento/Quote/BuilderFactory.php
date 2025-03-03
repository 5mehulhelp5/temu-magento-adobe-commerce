<?php

namespace M2E\Temu\Model\Magento\Quote;

class BuilderFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(
        \M2E\Temu\Model\Order\ProxyObject $proxyOrder,
        array $data = []
    ): Builder {
        $data['proxyOrder'] = $proxyOrder;

        return $this->objectManager->create(Builder::class, $data);
    }
}
