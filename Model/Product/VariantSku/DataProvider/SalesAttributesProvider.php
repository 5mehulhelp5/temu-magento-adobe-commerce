<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\VariantSku\DataProvider;

use M2E\Temu\Model\Product\DataProvider\DataBuilderHelpTrait;
use M2E\Temu\Model\Product\DataProvider\DataBuilderInterface;
use M2E\Temu\Model\Product\VariantSku\DataProvider\Attributes\Item;

class SalesAttributesProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'SalesAttributes';

    private \M2E\Temu\Model\Product\VariantSku\DataProvider\Attributes\Processor $attributeProcessor;

    public function __construct(
        \M2E\Temu\Model\Product\VariantSku\DataProvider\Attributes\Processor $attributeProcessor
    ) {
        $this->attributeProcessor = $attributeProcessor;
    }

    public function getSalesAttributesData(\M2E\Temu\Model\Product\VariantSku $product): array
    {
        $result = array_map(static function (Item $attribute) {
            return [
                'parent_spec_id' => $attribute->getParentSpecId(),
                'spec_id' => $attribute->getSpecId(),
                'value' => $attribute->getValue(),
                'value_id' => $attribute->getValueId()
            ];
        }, $this->attributeProcessor->getAttributes($product));
        $this->collectWarningMessages($this->attributeProcessor->getWarningMessages());

        return $result;
    }

    private function collectWarningMessages(array $messages): void
    {
        foreach ($messages as $message) {
            $this->addWarningMessage($message);
        }
    }
}
