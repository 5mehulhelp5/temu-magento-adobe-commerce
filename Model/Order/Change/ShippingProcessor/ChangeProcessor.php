<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Change\ShippingProcessor;

class ChangeProcessor
{
    private array $shippingProvidersNamesById = [];
    private \M2E\Temu\Model\Channel\Connector\Order\Send\Entity\Processor $sendEntityProcessor;
    private \M2E\Temu\Model\ShippingProvider\Repository $shippingProviderRepository;

    public function __construct(
        \M2E\Temu\Model\Channel\Connector\Order\Send\Entity\Processor $sendEntityProcessor,
        \M2E\Temu\Model\ShippingProvider\Repository $shippingProviderRepository
    ) {
        $this->sendEntityProcessor = $sendEntityProcessor;
        $this->shippingProviderRepository = $shippingProviderRepository;
    }

    public function process(
        \M2E\Temu\Model\Account $account,
        \M2E\Temu\Model\Order\Change $change
    ): ChangeResult {
        $order = $change->getOrder();
        $changeParams = $change->getParams();

        $trackingNumber = $changeParams['tracking_number'];
        $shippingProviderId = $changeParams['shipping_provider_id'];

        $orderBuilder = \M2E\Temu\Model\Channel\Connector\Order\Send\Entity\OrderBuilder::create();
        $orderBuilder->setOrderId($order->getChannelOrderId());

        $orderItems = $this->getOrderItemsForShipping($change);

        if (empty($orderItems)) {
            return ChangeResult::createSkipped();
        }

        $orderBuilder->setItems($orderItems);

        $response = $this->sendEntityProcessor->process(
            $account,
            $trackingNumber,
            $shippingProviderId,
            $orderBuilder->build()
        );

        if (!$response->isSuccess()) {
            return ChangeResult::createFailed(
                $orderItems,
                $trackingNumber,
                $this->getShippingProviderName($shippingProviderId),
                $response->getErrorMessages()
            );
        }

        return ChangeResult::createSuccess(
            $orderItems,
            $trackingNumber,
            $this->getShippingProviderName($shippingProviderId),
            $response->getErrorMessages(),
        );
    }

    /**
     * @param \M2E\Temu\Model\Order\Change $change
     *
     * @return \M2E\Temu\Model\Order\Item[]
     */
    private function getOrderItemsForShipping(\M2E\Temu\Model\Order\Change $change): array
    {
        $order = $change->getOrder();
        $changeParams = $change->getParams();

        $orderItemsForShipping = [];
        foreach ($changeParams['items'] as $orderItemData) {
            $itemId = (int)$orderItemData['item_id'];
            $orderItem = $order->findItem($itemId);

            if ($orderItem === null) {
                continue;
            }

            if (!$orderItem->isStatusUnshipped()) {
                continue;
            }

            $orderItemsForShipping[] = $orderItem;
        }

        return $orderItemsForShipping;
    }

    private function getShippingProviderName(int $id): string
    {
        if (isset($this->shippingProvidersNamesById[$id])) {
            return $this->shippingProvidersNamesById[$id];
        }

        $provider = $this->shippingProviderRepository->findByShippingProviderId($id);
        $providerName = '';
        if ($provider !== null) {
            $providerName = $provider->getShippingProviderName();
        }

        return $this->shippingProvidersNamesById[$id] = $providerName;
    }
}
