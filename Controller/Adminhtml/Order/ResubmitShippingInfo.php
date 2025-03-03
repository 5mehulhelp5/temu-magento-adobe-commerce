<?php

namespace M2E\Temu\Controller\Adminhtml\Order;

class ResubmitShippingInfo extends \M2E\Temu\Controller\Adminhtml\AbstractOrder
{
    private \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $orderShipmentCollectionFactory;
    private \M2E\Temu\Model\Order\ShipmentService $orderShipmentHandler;
    private \M2E\Temu\Model\Order\Repository $orderRepository;

    public function __construct(
        \M2E\Temu\Model\Order\Repository $orderRepository,
        \M2E\Temu\Model\Order\ShipmentService $orderShipmentHandler,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $orderShipmentCollectionFactory,
        \M2E\Temu\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->orderShipmentCollectionFactory = $orderShipmentCollectionFactory;
        $this->orderShipmentHandler = $orderShipmentHandler;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $orderIds = $this->getRequestIds();

        $isFail = false;

        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->find((int)$orderId);
            if ($order === null) {
                continue;
            }

            $shipmentsCollection = $this->orderShipmentCollectionFactory->create();
            $shipmentsCollection->setOrderFilter($order->getMagentoOrderId());

            foreach ($shipmentsCollection->getItems() as $shipment) {
                /** @var \Magento\Sales\Model\Order\Shipment $shipment */
                if (!$shipment->getId()) {
                    continue;
                }

                $result = $this->orderShipmentHandler->shipByShipment(
                    $order,
                    $shipment,
                    \M2E\Core\Helper\Data::INITIATOR_USER
                );

                if ($result === \M2E\Temu\Model\Order\ShipmentService::HANDLE_RESULT_FAILED) {
                    $isFail = true;
                }
            }
        }

        if ($isFail) {
            $errorMessage = __('Shipping Information was not resend.');
            if (count($orderIds) > 1) {
                $errorMessage = __('Shipping Information was not resend for some Orders.');
            }

            $this->messageManager->addError($errorMessage);
        } else {
            $this->messageManager->addSuccess(
                __('Shipping Information has been resend.')
            );
        }

        return $this->_redirect($this->redirect->getRefererUrl());
    }
}
