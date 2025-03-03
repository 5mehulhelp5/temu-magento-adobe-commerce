<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order\Item;

class ProductAssignService
{
    private \M2E\Temu\Model\Order\Item\Repository $orderItemRepository;

    public function __construct(
        \M2E\Temu\Model\Order\Item\Repository $orderItemRepository
    ) {
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * @param \M2E\Temu\Model\Order\Item $orderItem
     * @param \Magento\Catalog\Model\Product $magentoProduct
     * @param int $initiator
     *
     * @return void
     * @throws \M2E\Temu\Model\Exception\Logic
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function assign(
        \M2E\Temu\Model\Order\Item $orderItem,
        \Magento\Catalog\Model\Product $magentoProduct,
        int $initiator
    ): void {
        $orderItem->setMagentoProductId((int)$magentoProduct->getId());
        $this->orderItemRepository->save($orderItem);

        if ($initiator === \M2E\Core\Helper\Data::INITIATOR_EXTENSION) {
            return;
        }

        $orderItem->getOrder()->getLogService()->setInitiator($initiator);
        $sku = $orderItem->getProductSku();
        if ($sku === null) {
            $sku = $orderItem->getSkuId();
        }
        $orderItem->getOrder()->addSuccessLog(
            'Order Item "%title%" was Linked.',
            [
                'title' => $sku,
            ]
        );
    }

    /**
     * @param \M2E\Temu\Model\Order\Item $orderItem
     *
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function unAssign(\M2E\Temu\Model\Order\Item $orderItem): void
    {
        if ($orderItem->getOrder()->getReserve()->isPlaced()) {
            $orderItem->getOrder()->getReserve()->cancel();
        }

        $orderItem->removeMagentoProductId();
        $orderItem->removeAssociatedProducts();
        $orderItem->removeAssociatedOptions();

        $this->orderItemRepository->save($orderItem);

        $orderItem->getOrder()->getLogService()->setInitiator(\M2E\Core\Helper\Data::INITIATOR_USER);
        $sku = $orderItem->getProductSku();
        if ($sku === null) {
            $sku = $orderItem->getSkuId();
        }
        $orderItem->getOrder()->addSuccessLog(
            'Item "%title%" was Unlinked.',
            [
                'title' => $sku,
            ]
        );
    }
}
