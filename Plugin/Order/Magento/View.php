<?php

namespace M2E\Temu\Plugin\Order\Magento;

class View extends \M2E\Temu\Plugin\AbstractPlugin
{
    private \M2E\Temu\Model\Order\Repository $orderRepository;

    public function __construct(
        \M2E\Temu\Model\Order\Repository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @throws \M2E\Temu\Model\Exception
     */
    public function aroundSetLayout(
        \Magento\Framework\View\Element\AbstractBlock $interceptor,
        \Closure $callback,
        ...$arguments
    ) {
        if (!($interceptor instanceof \Magento\Sales\Block\Adminhtml\Order\View)) {
            return $callback(...$arguments);
        }

        return $this->execute('setLayout', $interceptor, $callback, $arguments);
    }

    protected function processSetLayout($interceptor, \Closure $callback, array $arguments)
    {
        /** @var \Magento\Sales\Block\Adminhtml\Order\View $interceptor */
        $magentoOrderId = $interceptor->getRequest()->getParam('order_id');
        if (empty($magentoOrderId)) {
            return $callback(...$arguments);
        }

        $order = $this->findOrder((int)$magentoOrderId);
        if ($order === null) {
            return $callback(...$arguments);
        }

        $buttonUrl = $interceptor->getUrl(
            'm2e_temu/order/view',
            ['id' => $order->getId()]
        );

        $interceptor->addButton(
            'go_to_temu_order',
            [
                'label' => __(
                    'Show %channel_title Order',
                    [
                        'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                    ],
                ),
                'onclick' => "setLocation('$buttonUrl')",
            ],
            0,
            -1
        );

        return $callback(...$arguments);
    }

    //########################################

    private function findOrder(int $magentoOrderId): ?\M2E\Temu\Model\Order
    {
        try {
            $order = $this->orderRepository->findByMagentoOrderId($magentoOrderId);

            if ($order === null) {
                return null;
            }
        } catch (\Throwable $exception) {
            return null;
        }

        return $order;
    }

    //########################################
}
