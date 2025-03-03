<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Product;

class ProductCollection
{
    /** @var \M2E\Temu\Model\Channel\Product[] */
    private array $products = [];

    public function empty(): bool
    {
        return empty($this->products);
    }

    public function has(string $channelId): bool
    {
        return isset($this->products[$channelId]);
    }

    public function add(\M2E\Temu\Model\Channel\Product $product): void
    {
        $this->products[$product->getChannelProductId()] = $product;
    }

    public function get(string $channelId): \M2E\Temu\Model\Channel\Product
    {
        return $this->products[$channelId];
    }

    public function remove(string $channelId): void
    {
        unset($this->products[$channelId]);
    }

    /**
     * @return \M2E\Temu\Model\Channel\Product[]
     */
    public function getAll(): array
    {
        return array_values($this->products);
    }

    /**
     * @return string[]
     */
    public function getProductsChannelIds(): array
    {
        return array_keys($this->products);
    }
}
