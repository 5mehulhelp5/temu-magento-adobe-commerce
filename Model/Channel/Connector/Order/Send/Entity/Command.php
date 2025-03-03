<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Order\Send\Entity;

class Command implements \M2E\Core\Model\Connector\CommandInterface
{
    private \M2E\Temu\Model\Channel\Connector\Order\Send\Entity\Order $order;
    private string $accountHash;
    private string $trackingNumber;
    private int $shippingProviderId;

    public function __construct(
        string $accountHash,
        \M2E\Temu\Model\Channel\Connector\Order\Send\Entity\Order $order,
        string $trackingNumber,
        int $shippingProviderId
    ) {
        $this->order = $order;
        $this->accountHash = $accountHash;
        $this->trackingNumber = $trackingNumber;
        $this->shippingProviderId = $shippingProviderId;
    }

    public function getCommand(): array
    {
        return ['Order', 'Send', 'Entity'];
    }

    public function getRequestData(): array
    {
        return [
            'account' => $this->accountHash,
            'shipping_provider_id' => $this->shippingProviderId,
            'tracking_number' => $this->trackingNumber,
            'order' => [
                'id' => $this->order->id,
                'items' => $this->order->orderItems,
            ],
        ];
    }

    public function parseResponse(
        \M2E\Core\Model\Connector\Response $response
    ): \M2E\Temu\Model\Channel\Connector\Order\Send\Entity\Response {
        $errorMessages = [];
        $warningMessages = [];

        foreach ($response->getMessageCollection()->getMessages() as $message) {
            if ($message->isError()) {
                $errorMessages[] = $message;
            }

            if ($message->isWarning()) {
                $warningMessages[] = $message;
            }
        }

        return new Response(
            empty($errorMessages),
            $errorMessages,
            $warningMessages
        );
    }
}
