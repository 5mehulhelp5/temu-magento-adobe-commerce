<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order;

class EventDispatcher
{
    private const CHANEL_NAME = 'temu';

    private const REGION_EUROPE = 'europe';
    private const REGION_AMERICA = 'america';
    private const REGION_ASIA_PACIFIC = 'asia-pacific';

    private const SITES_OF_AMERICAN_REGION = [
        \M2E\Temu\Model\Account::SITE_ID_CANADA,
        \M2E\Temu\Model\Account::SITE_ID_MEXICO,
    ];

    private \Magento\Framework\Event\ManagerInterface $eventManager;

    public function __construct(\Magento\Framework\Event\ManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function dispatchEventsMagentoOrderCreated(\M2E\Temu\Model\Order $order): void
    {
        $this->eventManager->dispatch('m2e_temu_order_place_success', ['order' => $order]);

        $this->eventManager->dispatch('ess_magento_order_created', [
            'channel' => self::CHANEL_NAME,
            'channel_order_id' => (int)$order->getId(),
            'channel_external_order_id' => $order->getChannelOrderId(),
            'magento_order_id' => (int)$order->getMagentoOrderId(),
            'magento_order_increment_id' => $order->getMagentoOrder()->getIncrementId(),
            'channel_purchase_date' => \DateTime::createFromImmutable($order->getPurchaseDate()),
            'region' => $this->resolveRegion($order->getAccount()),
        ]);
    }

    private function resolveRegion(\M2E\Temu\Model\Account $account): string
    {
        if ($account->isRegionEU()) {
            return self::REGION_EUROPE;
        }

        if (
            $account->isRegionUs()
            || in_array($account->getSiteId(), self::SITES_OF_AMERICAN_REGION)
        ) {
            return self::REGION_AMERICA;
        }

        return self::REGION_ASIA_PACIFIC;
    }

    public function dispatchEventInvoiceCreated(\M2E\Temu\Model\Order $order): void
    {
        $this->eventManager->dispatch('ess_order_invoice_created', [
            'channel' => self::CHANEL_NAME,
            'channel_order_id' => (int)$order->getId(),
        ]);
    }
}
