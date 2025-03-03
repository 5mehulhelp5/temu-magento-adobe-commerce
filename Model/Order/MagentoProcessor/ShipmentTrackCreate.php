<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\MagentoProcessor;

class ShipmentTrackCreate
{
    private \M2E\Temu\Model\Magento\Order\Shipment\TrackFactory $magentoOrderShipmentTrackFactory;

    public function __construct(
        \M2E\Temu\Model\Magento\Order\Shipment\TrackFactory $magentoOrderShipmentTrackFactory
    ) {
        $this->magentoOrderShipmentTrackFactory = $magentoOrderShipmentTrackFactory;
    }

    public function process(\M2E\Temu\Model\Order $order): void
    {
        if (!$this->canCreateTracks($order)) {
            return;
        }

        $tracks = [];

        /** @var \Magento\Sales\Model\Order $magentoOrder */
        $magentoOrder = $order->getMagentoOrder();

        try {
            $trackBuilder = $this->magentoOrderShipmentTrackFactory
                ->create($magentoOrder, $order->getShippingTrackingDetails());

            $tracks = $trackBuilder->create();
        } catch (\Throwable $throwable) {
            $order->addErrorLog(
                'Tracking details were not imported. Reason: %msg%',
                ['msg' => $throwable->getMessage()]
            );
        }

        if (!empty($tracks)) {
            $order->addSuccessLog('Tracking details were imported.');
        }
    }

    private function canCreateTracks(\M2E\Temu\Model\Order $order): bool
    {
        if (!$order->hasMagentoOrder()) {
            return false;
        }

        $trackingDetails = $order->getShippingTrackingDetails();
        if (empty($trackingDetails)) {
            return false;
        }

        $magentoOrder = $order->getMagentoOrder();
        if ($magentoOrder === null) {
            return false;
        }

        if (!$magentoOrder->hasShipments()) {
            return false;
        }

        return true;
    }
}
