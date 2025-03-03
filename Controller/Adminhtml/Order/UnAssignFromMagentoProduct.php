<?php

namespace M2E\Temu\Controller\Adminhtml\Order;

use M2E\Temu\Controller\Adminhtml\AbstractOrder;

class UnAssignFromMagentoProduct extends AbstractOrder
{
    private \M2E\Temu\Model\Order\Item\Repository $orderItemRepository;
    private \M2E\Temu\Model\Order\Item\ProductAssignService $productAssignService;

    public function __construct(
        \M2E\Temu\Model\Order\Item\Repository $orderItemRepository,
        \M2E\Temu\Model\Order\Item\ProductAssignService $productAssignService,
        $context = null
    ) {
        parent::__construct($context);
        $this->orderItemRepository = $orderItemRepository;
        $this->productAssignService = $productAssignService;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $orderItemId = (int)$this->getRequest()->getParam('order_item_id');
        $orderItem = $this->orderItemRepository->find($orderItemId);

        if ($orderItem === null) {
            $this->setJsonContent(['error' => __('Please specify Required Options.')]);

            return $this->getResult();
        }

        $this->productAssignService->unAssign($orderItem);

        $this->setJsonContent([
            'success' => __('Item was Unlinked.'),
        ]);

        return $this->getResult();
    }
}
