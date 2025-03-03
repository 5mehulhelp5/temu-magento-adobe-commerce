<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Product\DataProvider\Variants;

class Item
{
    private string $skuId;
    private int $qty;
    private float $price;
    private string $currency;

    public function setSkuId(string $skuId): void
    {
        $this->skuId = $skuId;
    }

    public function getSkuId(): string
    {
        return $this->skuId;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setQty(int $qty)
    {
        $this->qty = $qty;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->skuId,
            'price' => $this->price,
            'currency_code' => $this->currency,
            'qty' => $this->qty,
        ];

        return $data;
    }
}
