<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Dashboard\Products;

class DataProvider implements \M2E\Core\Model\Dashboard\Products\DataProviderInterface
{
    use \M2E\Temu\Model\Dashboard\CacheIntValueTrait;

    private const CACHE_LIFE_TIME = 600; // 10 min

    private \M2E\Temu\Model\Product\Repository $productRepository;

    public function __construct(
        \M2E\Temu\Model\Product\Repository $productRepository,
        \M2E\Temu\Helper\Data\Cache\Permanent $cache
    ) {
        $this->productRepository = $productRepository;
        $this->cache = $cache;
    }

    public function getCountOfListedProducts(): int
    {
        return $this->getCachedValue(__METHOD__, self::CACHE_LIFE_TIME, function () {
            return $this->productRepository->getCountOfListedProducts();
        });
    }

    public function getCountOfNotListedProducts(): int
    {
        return $this->getCachedValue(__METHOD__, self::CACHE_LIFE_TIME, function () {
            return $this->productRepository->getCountOfNotListedProducts();
        });
    }
}
