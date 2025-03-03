<?php

declare(strict_types=1);

namespace M2E\Temu\Model\InventorySync;

use M2E\Temu\Model\Product;

class ProductBuilder
{
    private const PRODUCT_STATUS_MAPPING = [
        'active' => Product::STATUS_LISTED,
        'inactive' => Product::STATUS_INACTIVE,
    ];

    private \M2E\Temu\Model\Account $account;

    public function __construct(
        \M2E\Temu\Model\Account $account
    ) {
        $this->account = $account;
    }

    public function build(array $channelRawProducts): \M2E\Temu\Model\Channel\Product\ProductCollection
    {
        $result = new \M2E\Temu\Model\Channel\Product\ProductCollection();
        foreach ($channelRawProducts as $channelRawProduct) {
            $variantSkusCollection = $this->buildVariantSkusCollection($channelRawProduct['skus']);

            if (count($variantSkusCollection->getAll()) !== 1) {
                continue;
            }

            $channelProduct = new \M2E\Temu\Model\Channel\Product(
                $this->account->getId(),
                $channelRawProduct['id'],
                $channelRawProduct['title'],
                $channelRawProduct['image_url'],
                $variantSkusCollection,
                (int)$channelRawProduct['category_id'],
                (int)$channelRawProduct['shipping_template_id'],
                $this->mapChannelStatusOnExtension($channelRawProduct['status'])
            );

            $result->add($channelProduct);
        }

        return $result;
    }

    private function buildVariantSkusCollection(
        array $channelVariantsRawArray
    ): \M2E\Temu\Model\Channel\Product\VariantSku\VariantSkuCollection {
        $variantSkusCollection = new \M2E\Temu\Model\Channel\Product\VariantSku\VariantSkuCollection();
        foreach ($channelVariantsRawArray as $sku => $variantSkuArray) {
            $variantSkuObj = new \M2E\Temu\Model\Channel\Product\VariantSku(
                $this->account->getId(),
                (string)$variantSkuArray['id'],
                $variantSkuArray['sku'],
                $variantSkuArray['image_url'],
                $variantSkuArray['qty'],
                $variantSkuArray['price_base'],
                $variantSkuArray['price_retail'],
                $this->mapChannelStatusOnExtension($variantSkuArray['status']),
                $variantSkuArray['currency_code'],
                $variantSkuArray['specification'],
                [],
                $variantSkuArray['system']['qty_request_time'],
                $variantSkuArray['system']['price_request_time']
            );

            $variantSkusCollection->add($variantSkuObj);
        }

        return $variantSkusCollection;
    }

    private function mapChannelStatusOnExtension($status)
    {
        return self::PRODUCT_STATUS_MAPPING[$status] ?? Product::STATUS_INACTIVE;
    }
}
