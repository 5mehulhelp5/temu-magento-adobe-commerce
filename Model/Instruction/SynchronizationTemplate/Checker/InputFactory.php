<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Instruction\SynchronizationTemplate\Checker;

class InputFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param \M2E\Temu\Model\Product $product
     * @param \M2E\Temu\Model\Instruction[] $instructions
     *
     * @return \M2E\Temu\Model\Instruction\SynchronizationTemplate\Checker\Input
     */
    public function create(\M2E\Temu\Model\Product $product, array $instructions): Input
    {
        return $this->objectManager->create(
            Input::class,
            [
                'product' => $product,
                'instructions' => $instructions,
            ],
        );
    }
}
