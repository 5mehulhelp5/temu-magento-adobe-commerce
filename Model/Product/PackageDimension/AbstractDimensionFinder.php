<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\PackageDimension;

use M2E\Temu\Model\Settings;

abstract class AbstractDimensionFinder
{
    private \M2E\Temu\Model\Settings $settings;

    public function __construct(\M2E\Temu\Model\Settings $settings)
    {
        $this->settings = $settings;
    }

    abstract public function find(\M2E\Temu\Model\Product $product): object;

    /**
     * @throws \M2E\Temu\Model\Product\PackageDimension\NotFoundAttributeValueException
     * @throws \M2E\Temu\Model\Product\PackageDimension\NotConfiguredException
     */
    protected function getPackageDimensionValue(
        string $type,
        \M2E\Temu\Model\Magento\Product $magentoProduct
    ): float {
        $mode = $this->settings->getPackageDimensionMode($type);
        if ($mode === Settings::PACKAGE_MODE_CUSTOM_VALUE) {
            return $this->getValueFromCustomValue($type);
        }

        if ($mode === Settings::PACKAGE_MODE_CUSTOM_ATTRIBUTE) {
            $attributeCode = $this->settings->getPackageDimensionCustomAttribute($type);
            if (empty($attributeCode)) {
                throw new NotConfiguredException($type);
            }

            return $this->getValueFromMagentoAttribute($type, $attributeCode, $magentoProduct);
        }

        throw new NotConfiguredException($type);
    }

    /**
     * @throws \M2E\Temu\Model\Product\PackageDimension\NotConfiguredException
     */
    private function getValueFromCustomValue(string $type): float
    {
        $customValue = $this->settings->getPackageDimensionCustomValue($type);

        if (empty($customValue)) {
            throw new NotConfiguredException($type);
        }

        return (float)$customValue;
    }

    /**
     * @throws \M2E\Temu\Model\Product\PackageDimension\NotFoundAttributeValueException
     */
    private function getValueFromMagentoAttribute(
        $type,
        string $attributeCode,
        \M2E\Temu\Model\Magento\Product $magentoProduct
    ): float {
        $attributeValue = (float)$magentoProduct->getAttributeValue($attributeCode);

        if (empty($attributeValue)) {
            throw new NotFoundAttributeValueException($type, $attributeCode);
        }

        return $attributeValue;
    }

    protected function toInteger(float $length): int
    {
        return (int)ceil($length);
    }

    protected function toFloat(float $length): float
    {
        return round($length, 2);
    }
}
