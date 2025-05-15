<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\ListAction;

class Request extends \M2E\Temu\Model\Product\Action\AbstractRequest
{
    use \M2E\Temu\Model\Product\Action\RequestTrait;

    private array $metadata = [];

    public function getActionData(
        \M2E\Temu\Model\Product $product,
        \M2E\Temu\Model\Product\Action\Configurator $actionConfigurator,
        \M2E\Temu\Model\Product\Action\VariantSettings $variantSettings,
        array $params
    ): array {
        $dataProvider = $product->getDataProvider();
        $variantSkus = $dataProvider->getVariantSkusForList()->getValue();
        $attributes = $dataProvider->getProductAttributesData()->getValue();

        $request['title'] = $dataProvider->getTitle()->getValue();
        $request['description'] = $dataProvider->getDescription()->getValue()->description;
        $request['currency_code'] = $product->getCurrencyCode();
        $request['bullet_points'] = [];

        $request['images'] = array_map(
            static function (\M2E\Temu\Model\Product\DataProvider\Images\Image $image) {
                return $image->url;
            },
            $dataProvider->getImages()->getValue()->set
        );

        $request['category_id'] = $dataProvider->getCategoryData()->getValue();

        $request['attributes'] = array_map(
            static function (\M2E\Temu\Model\Product\DataProvider\Attributes\Item $attribute) {
                return [
                    'pid' => $attribute->getPid(),
                    'ref_pid' => $attribute->getRefPid(),
                    'template_pid' => $attribute->getTemplatePid(),
                    'value' => $attribute->getValue(),
                    'value_id' => $attribute->getValueId(),
                    'name' => $attribute->getName(),
                ];
            },
            $attributes->items
        );

        $request['size_charts'] = [];

        $request['skus'] = array_map(
            static function (\M2E\Temu\Model\Product\DataProvider\Variants\Item $item) {
                return [
                    'sku' => $item->getSku(),
                    'identifier' => $item->getIdentifier(),
                    'price_base' => $item->getPrice(),
                    'qty' => $item->getQty(),
                    'images' => $item->getImages(),
                    'variation_attributes' => $item->getVariationAttributes(),
                    'package_weight' => $item->getPackageWeight(),
                    'package_dimensions' => $item->getPackageDimensions(),
                ];
            },
            $variantSkus->items
        );

        $request['shipping'] = [
            'template_id' => $dataProvider->getShipping()->getValue()->shippingTemplateId,
            'limit_day' => $dataProvider->getShipping()->getValue()->preparationTime,
        ];

        $this->metadata = $dataProvider->getMetaData();

        $this->processDataProviderLogs($dataProvider);

        return $request;
    }

    protected function getActionMetadata(): array
    {
        return $this->metadata;
    }
}
