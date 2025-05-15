<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\Action\Type\Revise;

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
        $variantSkus = $dataProvider->getVariantSkusForRevise()->getValue();
        $request['id'] = $product->getChannelProductId();

        $request['skus'] = array_map(
            static function (\M2E\Temu\Model\Product\DataProvider\Variants\Item $item) {
                return [
                    'id' => $item->getSkuId(),
                    'price' => $item->getPrice(),
                    'currency_code' => $item->getCurrency(),
                    'qty' => $item->getQty(),
                ];
            },
            $variantSkus->items
        );

        $this->metadata = $dataProvider->getMetaData();

        $this->processDataProviderLogs($dataProvider);

        return $request;
    }

    protected function getActionMetadata(): array
    {
        return $this->metadata;
    }
}
