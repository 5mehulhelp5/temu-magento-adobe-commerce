<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Shipment\Data;

class TrackingDetails
{
    private int $magentoShipmentId;
    private string $carrierCode;
    private string $carrierTitle;
    private string $carrierName;
    private string $trackingNumber;

    public function __construct(
        int $magentoShipmentId,
        string $carrierCode,
        string $carrierTitle,
        string $carrierName,
        string $trackingNumber
    ) {
        $this->magentoShipmentId = $magentoShipmentId;
        $this->carrierCode = $carrierCode;
        $this->carrierTitle = $carrierTitle;
        $this->carrierName = $carrierName;
        $this->trackingNumber = $trackingNumber;
    }

    public function getMagentoShipmentId(): int
    {
        return $this->magentoShipmentId;
    }

    public function getCarrierCode(): string
    {
        return $this->carrierCode;
    }

    public function getCarrierTitle(): string
    {
        return $this->carrierTitle;
    }

    public function getCarrierName(): string
    {
        return $this->carrierName;
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }
}
