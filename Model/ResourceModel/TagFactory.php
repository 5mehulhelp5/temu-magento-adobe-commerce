<?php

namespace M2E\Temu\Model\ResourceModel;

class TagFactory
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Tag
    {
        return $this->objectManager->create(Tag::class);
    }
}
