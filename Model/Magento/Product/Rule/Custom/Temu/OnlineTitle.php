<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Magento\Product\Rule\Custom\Temu;

class OnlineTitle extends \M2E\Temu\Model\Magento\Product\Rule\Custom\AbstractCustomFilter
{
    public const NICK = 'online_title';

    public function getLabel(): string
    {
        return (string)__('Title');
    }

    public function getValueByProductInstance(\Magento\Catalog\Model\Product $product)
    {
        return $product->getData('online_title');
    }
}
