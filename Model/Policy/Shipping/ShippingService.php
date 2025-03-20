<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Policy\Shipping;

class ShippingService
{
    private const CACHE_KEY_SHIPPING_TEMPLATES = 'shipping_templates';
    private const CACHE_LIFETIME_THIRTY_MINUTES = 1800;

    private \M2E\Temu\Helper\Data\Cache\Permanent $cache;
    private \M2E\Temu\Model\Channel\Policy\Shipping\TemplateService $deliveryTemplateService;

    public function __construct(
        \M2E\Temu\Model\Channel\Policy\Shipping\TemplateService $deliveryTemplateService,
        \M2E\Temu\Helper\Data\Cache\Permanent $cache
    ) {
        $this->cache = $cache;
        $this->deliveryTemplateService = $deliveryTemplateService;
    }

    public function getAllTemplates(
        \M2E\Temu\Model\Account $account,
        bool $force
    ): \M2E\Temu\Model\Channel\Policy\Shipping\Template\Collection {
        if (!$force) {
            $cachedData = $this->fromCache($this->createCacheKey($account));
            if ($cachedData !== null) {
                return \M2E\Temu\Model\Channel\Policy\Shipping\Template\Collection::createFromArray($cachedData);
            }
        }

        $this->clearCache($this->createCacheKey($account));

        $templateCollection = $this->deliveryTemplateService->retrieve($account);
        if ($templateCollection->isEmpty()) {
            return $templateCollection;
        }

        $this->toCache(
            $templateCollection->toArray(),
            $this->createCacheKey($account)
        );

        return $templateCollection;
    }

    // ----------------------------------------

    private function createCacheKey(
        \M2E\Temu\Model\Account $account
    ): string {
        return self::CACHE_KEY_SHIPPING_TEMPLATES . $account->getId();
    }

    private function toCache(array $data, string $key): void
    {
        $this->cache->setValue($key, $data, [], self::CACHE_LIFETIME_THIRTY_MINUTES);
    }

    private function fromCache(string $key): ?array
    {
        return $this->cache->getValue($key);
    }

    private function clearCache(string $key): void
    {
        $this->cache->removeValue($key);
    }
}
