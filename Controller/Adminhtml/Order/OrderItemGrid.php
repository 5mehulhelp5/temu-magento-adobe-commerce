<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Order;

class OrderItemGrid extends \M2E\Temu\Controller\Adminhtml\Order\AbstractOrder
{
    private \M2E\Temu\Model\Order\Repository $orderRepository;

    public function __construct(
        \M2E\Temu\Model\Order\Repository $orderRepository
    ) {
        parent::__construct();
        $this->orderRepository = $orderRepository;
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('id');

        $order = $this->orderRepository->find((int)$orderId);

        if ($order === null) {
            $this->setJsonContent([
                'error' => __('Please specify Required Options.'),
            ]);

            return $this->getResult();
        }

        $orderItemsBlock = $this
            ->getLayout()
            ->createBlock(\M2E\Temu\Block\Adminhtml\Order\View\Item::class, '', [
                'order' => $order,
            ]);

        $this->setAjaxContent($orderItemsBlock->toHtml());

        return $this->getResult();
    }
}
