<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider;

class DescriptionProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'Description';
    private string $descriptionHash = '';

    public function getDescription(\M2E\Temu\Model\Product $product): Description\Value
    {
        $data = $product->getRenderedDescription();

        $hash = \M2E\Core\Helper\Data::md5String($data);
        $this->descriptionHash = $hash;

        return new Description\Value($data, $hash);
    }

    public function getMetaData(): array
    {
        return [
            self::NICK => ['online_description' => $this->descriptionHash],
        ];
    }
}
