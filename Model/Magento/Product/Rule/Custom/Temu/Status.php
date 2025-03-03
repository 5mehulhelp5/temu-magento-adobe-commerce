<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Magento\Product\Rule\Custom\Temu;

use M2E\Temu\Model\Magento\Product\Rule\Condition\AbstractModel;

class Status extends \M2E\Temu\Model\Magento\Product\Rule\Custom\AbstractCustomFilter
{
    public const NICK = 'status';

    public function getInputType(): string
    {
        return AbstractModel::INPUT_TYPE_SELECT;
    }

    public function getValueElementType(): string
    {
        return AbstractModel::VALUE_ELEMENT_TYPE_SELECT;
    }

    public function getOptions(): array
    {
        return [
            [
                'value' => \M2E\Temu\Model\Product::STATUS_NOT_LISTED,
                'label' => __('Not Listed'),
            ],
            [
                'value' => \M2E\Temu\Model\Product::STATUS_LISTED,
                'label' => __('Active'),
            ],
            [
                'value' => \M2E\Temu\Model\Product::STATUS_BLOCKED,
                'label' => __('Inactive'),
            ],
            [
                'value' => \M2E\Temu\Model\Product::STATUS_INACTIVE,
                'label' => __('Incomplete'),
            ],
        ];
    }

    public function getLabel(): string
    {
        return (string)__('Status');
    }

    public function getValueByProductInstance(\Magento\Catalog\Model\Product $product)
    {
        return $product->getData('status');
    }
}
