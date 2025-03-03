<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order;

class DeleteService
{
    private \M2E\Temu\Model\Order\Repository $orderRepository;
    private \M2E\Temu\Helper\Module\Exception $exceptionHelper;

    public function __construct(
        \M2E\Temu\Model\Order\Repository $orderRepository,
        \M2E\Temu\Helper\Module\Exception $exceptionHelper
    ) {
        $this->orderRepository = $orderRepository;
        $this->exceptionHelper = $exceptionHelper;
    }

    public function deleteByAccountId(int $accountId): void
    {
        try {
            $this->orderRepository->removeRelatedOrderChangesByAccountId($accountId);
            $this->orderRepository->removeRelatedOrderItemsByAccountId($accountId);
            $this->orderRepository->removeRelatedOrderNoteByAccountId($accountId);
            $this->orderRepository->removeByAccountId($accountId);
        } catch (\Throwable $e) {
            $this->exceptionHelper->process($e);
            throw $e;
        }
    }
}
