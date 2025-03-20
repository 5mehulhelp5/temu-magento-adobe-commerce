<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider;

class ProductAttributesProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'ProductAttributes';
    private string $attributesData = '';

    private Attributes\Processor $attributeProcessor;

    public function __construct(
        Attributes\Processor $attributeProcessor
    ) {
        $this->attributeProcessor = $attributeProcessor;
    }

    public function getProductAttributesData(\M2E\Temu\Model\Product $product): array
    {
        $attributes = $this->attributeProcessor->execute($product);

        $this->collectWarningMessages($this->attributeProcessor->getWarningMessages());
        $this->attributesData = json_encode($attributes);

        return $attributes;
    }

    public function getMetaData(): array
    {
        return [
            self::NICK => $this->attributesData
        ];
    }

    private function collectWarningMessages(array $messages): void
    {
        foreach ($messages as $message) {
            $this->addWarningMessage($message);
        }
    }
}
