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

        $request['title'] = $dataProvider->getTitle()->getValue();
        $request['description'] = $dataProvider->getDescription()->getValue()->description;
        $request['currency_code'] = $product->getCurrencyCode();
        $request['bullet_points'] = [];
        $request['images'] = $dataProvider->getImages()->getValue();
        $request['category_id'] = $dataProvider->getCategoryData()->getValue();
        $request['attributes'] = $dataProvider->getProductAttributesData()->getValue();
        $request['size_charts'] = [];
        $request['skus'] = $variantSkus;

        $request['shipping'] = $dataProvider->getShipping()->getValue();

        $this->metadata = $dataProvider->getMetaData();

        $this->processDataProviderLogs($dataProvider);

        return $request;
    }

    protected function getActionMetadata(): array
    {
        return $this->metadata;
    }
}
