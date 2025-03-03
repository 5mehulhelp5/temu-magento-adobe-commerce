<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order;

class StatusResolver
{
    public static function resolve(string $channelStatus): int
    {
        $channelStatus = mb_strtolower($channelStatus);

        if ($channelStatus === \M2E\Temu\Model\Channel\Order::STATUS_PENDING) {
            return \M2E\Temu\Model\Order::STATUS_PENDING;
        }

        if ($channelStatus === \M2E\Temu\Model\Channel\Order::STATUS_UNSHIPPED) {
            return \M2E\Temu\Model\Order::STATUS_UNSHIPPED;
        }

        if (
            $channelStatus === \M2E\Temu\Model\Channel\Order::STATUS_PARTIALLY_SHIPPED
            || $channelStatus === \M2E\Temu\Model\Channel\Order::STATUS_PARTIALLY_DELIVERED
        ) {
            return \M2E\Temu\Model\Order::STATUS_PARTIALLY_SHIPPED;
        }

        if (
            $channelStatus === \M2E\Temu\Model\Channel\Order::STATUS_SHIPPED
            || $channelStatus === \M2E\Temu\Model\Channel\Order::STATUS_DELIVERED
        ) {
            return \M2E\Temu\Model\Order::STATUS_SHIPPED;
        }

        if (
            $channelStatus === \M2E\Temu\Model\Channel\Order::STATUS_CANCEL
        ) {
            return \M2E\Temu\Model\Order::STATUS_CANCELED;
        }

        return \M2E\Temu\Model\Order::STATUS_UNKNOWN;
    }
}
