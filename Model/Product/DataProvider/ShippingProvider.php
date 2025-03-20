<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider;

class ShippingProvider implements DataBuilderInterface
{
    public const NICK = 'Shipping';
    private array $shipping = [];

    public function getShippingData(\M2E\Temu\Model\Product $product): ?array
    {
        $listing = $product->getListing();

        if (!$listing->hasTemplateShipping()) {
            return null;
        }

        $shippingPolicy = $product->getShippingTemplate();

        $shippingData =  [
            'template_id' => $shippingPolicy->getShippingTemplateId(),
            'limit_day' => $shippingPolicy->getPreparationTime(),
        ];

        $this->shipping = $shippingData;

        return $shippingData;
    }

    public function getWarningMessages(): array
    {
        return [];
    }

    public function getMetaData(): array
    {
        return [
            self::NICK => ['shipping' => $this->shipping],
        ];
    }
}
