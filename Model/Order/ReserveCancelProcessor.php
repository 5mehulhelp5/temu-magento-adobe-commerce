<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order;

class ReserveCancelProcessor
{
    private \M2E\Temu\Model\Order\Repository $orderRepository;

    public function __construct(
        \M2E\Temu\Model\Order\Repository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \M2E\Temu\Model\Account $account
     *
     * @return void
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    public function process(\M2E\Temu\Model\Account $account): void
    {
        foreach ($this->orderRepository->findForReleaseReservation($account) as $order) {
            /** @var \M2E\Temu\Model\Order $order */
            $order->getReserve()->release();
        }
    }
}
