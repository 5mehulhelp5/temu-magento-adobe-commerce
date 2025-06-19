<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider;

class CategoryProvider implements DataBuilderInterface
{
    use DataBuilderHelpTrait;

    public const NICK = 'Category';
    private ?int $onlineCategoryId = null;

    public function getCategoryData(\M2E\Temu\Model\Product $product): ?int
    {
        try {
            $category = $product->getCategoryDictionary();
            $this->onlineCategoryId = (int)$category->getCategoryId();
        } catch (\Throwable $exception) {
        }

        return $this->onlineCategoryId;
    }

    public function getMetaData(): array
    {
        return [
            self::NICK => $this->onlineCategoryId
        ];
    }
}
