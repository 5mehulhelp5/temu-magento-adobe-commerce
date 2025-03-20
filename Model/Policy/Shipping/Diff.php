<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Policy\Shipping;

class Diff extends \M2E\Temu\Model\ActiveRecord\Diff
{
    public function isDifferent(): bool
    {
        return $this->isShippingDataDifferent();
    }

    public function isShippingDataDifferent(): bool
    {
        $keys = [
            \M2E\Temu\Model\ResourceModel\Policy\Shipping::COLUMN_SHIPPING_TEMPLATE_ID,
            \M2E\Temu\Model\ResourceModel\Policy\Shipping::COLUMN_PREPARATION_TIME,
        ];

        return $this->isSettingsDifferent($keys);
    }
}
