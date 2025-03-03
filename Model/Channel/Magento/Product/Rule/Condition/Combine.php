<?php

namespace M2E\Temu\Model\Channel\Magento\Product\Rule\Condition;

use M2E\Temu\Model\Magento\Product\Rule\Custom\Temu as TemuCustomFilters;

class Combine extends \M2E\Temu\Model\Magento\Product\Rule\Condition\Combine
{
    public function __construct(
        \M2E\Temu\Model\Magento\Product\Rule\Condition\ProductFactory $ruleConditionProductFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Rule\Model\Condition\Context $context,
        array $data = []
    ) {
        parent::__construct($ruleConditionProductFactory, $objectManager, $context, $data);

        $this->setType(self::class);
    }

    protected function getConditionCombine(): string
    {
        return $this->getType() . '|temu|';
    }

    protected function getCustomLabel(): string
    {
        return \M2E\Temu\Helper\Module::getExtensionTitle() . ' Values';
    }

    protected function getCustomOptions(): array
    {
        $attributes = $this->getCustomOptionsAttributes();

        if (empty($attributes)) {
            return [];
        }

        return $this->getOptions(
            \M2E\Temu\Model\Channel\Magento\Product\Rule\Condition\Product::class,
            $attributes,
            ['temu']
        );
    }

    protected function getCustomOptionsAttributes(): array
    {
        return [
            TemuCustomFilters\OnlineQty::NICK => \__('Available QTY'),
            TemuCustomFilters\OnlineTitle::NICK => \__('Title'),
            TemuCustomFilters\Status::NICK => \__('Status'),
            TemuCustomFilters\OnlinePrice::NICK => \__('Price'),
        ];
    }
}
