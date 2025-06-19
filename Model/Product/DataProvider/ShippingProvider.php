<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider;

class ShippingProvider implements DataBuilderInterface
{
    public const NICK = 'Shipping';
    private string $templateId = '';
    private int $limitDay;

    public function getShippingData(\M2E\Temu\Model\Product $product): ?\M2E\Temu\Model\Product\DataProvider\Shipping\Value
    {
        $listing = $product->getListing();

        if (!$listing->hasTemplateShipping()) {
            return null;
        }

        $shippingPolicy = $product->getShippingTemplate();

        $shippingTemplateId = $shippingPolicy->getShippingTemplateId();
        $shippingLimitDay = $shippingPolicy->getPreparationTime();

        $this->templateId = $shippingTemplateId;
        $this->limitDay = $shippingLimitDay;

        return new \M2E\Temu\Model\Product\DataProvider\Shipping\Value(
            $shippingTemplateId,
            $shippingLimitDay
        );
    }

    public function getWarningMessages(): array
    {
        return [];
    }

    public function getMetaData(): array
    {
        return [
            self::NICK => [
                'template_id' => $this->templateId,
                'limit_day' => $this->limitDay,
            ],
        ];
    }
}
