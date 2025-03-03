<?php

declare(strict_types=1);

namespace M2E\Temu\Model;

interface ProductInterface
{
    public function getListing(): Listing;

    public function getSellingFormatTemplate(): \M2E\Temu\Model\Policy\SellingFormat;
    public function getMagentoProduct(): \M2E\Temu\Model\Magento\Product\Cache;
    public function getMagentoProductId(): int;
    public function getId(): ?int;
}
