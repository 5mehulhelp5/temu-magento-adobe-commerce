<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product;

use M2E\Temu\Model\Channel\Product as ChannelProduct;
use M2E\Temu\Model\Product as ExtensionProduct;

class CalculateStatusByChannel
{
    public function calculate(ExtensionProduct $product, ChannelProduct $channelProduct): ?CalculateStatusByChannel\Result
    {
        if ($this->isStatusRight($product, $channelProduct->getStatus())) {
            return null;
        }

        $actionMessage = new \M2E\Temu\Model\Listing\Log\Record(
            (string)__(
                'Item Status was changed from "%from" to "%to".',
                [
                    'from' => \M2E\Temu\Model\Product::getStatusTitle($product->getStatus()),
                    'to' => \M2E\Temu\Model\Product::getStatusTitle($channelProduct->getStatus()),
                ],
            ),
            \M2E\Temu\Model\Log\AbstractModel::TYPE_SUCCESS,
        );

        return new CalculateStatusByChannel\Result(
            $product,
            $channelProduct->getStatus(),
            $actionMessage,
        );
    }

    private function isStatusRight(ExtensionProduct $product, int $channelStatus): bool
    {
        return $product->getStatus() === $channelStatus;
    }
}
