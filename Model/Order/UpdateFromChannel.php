<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Order;

class UpdateFromChannel
{
    private \M2E\Temu\Model\Account $account;
    private bool $isNeedValidateByCreateDate;
    private \DateTimeImmutable $borderCreateDate;
    private \DateTimeImmutable $accountCreateDate;
    /** @var \M2E\Temu\Model\Order\UpdateFromChannel\UpdateFactory */
    private UpdateFromChannel\UpdateFactory $updateFactory;
    /** @var \M2E\Temu\Model\Order\UpdateFromChannel\CreateFactory */
    private UpdateFromChannel\CreateFactory $createFactory;
    /** @var \M2E\Temu\Model\Order\Repository */
    private Repository $orderRepository;
    private \M2E\Temu\Model\Synchronization\LogService $syncLogService;
    private \M2E\Temu\Helper\Module\Exception $exceptionHelper;

    public function __construct(
        \M2E\Temu\Model\Account $account,
        bool $isNeedValidateByCreateDate,
        \M2E\Temu\Model\Order\Repository $orderRepository,
        \M2E\Temu\Model\Order\UpdateFromChannel\CreateFactory $createFactory,
        \M2E\Temu\Model\Order\UpdateFromChannel\UpdateFactory $updateFactory,
        \M2E\Temu\Model\Synchronization\LogService $syncLogService,
        \M2E\Temu\Helper\Module\Exception $exceptionHelper
    ) {
        $this->account = $account;
        $this->accountCreateDate = $account->getCreateData();
        $this->isNeedValidateByCreateDate = $isNeedValidateByCreateDate;
        $this->syncLogService = $syncLogService;
        $this->exceptionHelper = $exceptionHelper;
        $this->orderRepository = $orderRepository;
        $this->updateFactory = $updateFactory;
        $this->createFactory = $createFactory;
    }

    /**
     * @param \M2E\Temu\Model\Channel\Order[] $channelOrders
     *
     * @return \M2E\Temu\Model\Order[]
     */
    public function process(array $channelOrders): array
    {
        $result = [];
        foreach ($channelOrders as $channelOrder) {
            try {
                if (!$this->isValid($channelOrder)) {
                    continue;
                }

                $order = $this->findExistOrder($channelOrder);
                if ($order !== null) {
                    $this->update($order, $channelOrder);
                } else {
                    $order = $this->create($channelOrder);
                }
                /** @psalm-suppress RedundantCondition */
                if ($order !== null) {
                    $result[] = $order;
                }
            } catch (\Throwable $exception) {
                $this->syncLogService->addFromException($exception);
                $this->exceptionHelper->process($exception);

                continue;
            }
        }

        return $result;
    }

    // ----------------------------------------

    private function isValid(\M2E\Temu\Model\Channel\Order $channelOrder): bool
    {
        if (!$this->isNeedValidateByCreateDate) {
            return true;
        }

        $borderCreateDate = $this->getBorderCreateDate();
        $accountCreateDate = $this->accountCreateDate;

        $channelOrderCreateDate = $channelOrder->getCreateDate();

        return $channelOrderCreateDate->getTimestamp() >= $accountCreateDate->getTimestamp()
            && $channelOrderCreateDate->getTimestamp() >= $borderCreateDate->getTimestamp();
    }

    private function getBorderCreateDate(): \DateTimeImmutable
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->borderCreateDate)) {
            $this->borderCreateDate = \M2E\Core\Helper\Date::createImmutableCurrentGmt()->modify('-90 days');
        }

        return $this->borderCreateDate;
    }

    // ----------------------------------------

    private function findExistOrder(\M2E\Temu\Model\Channel\Order $channelOrder): ?\M2E\Temu\Model\Order
    {
        return $this->orderRepository->findByAccountAndChannelId(
            (int)$this->account->getId(),
            $channelOrder->getOrderId()
        );
    }

    private function create(\M2E\Temu\Model\Channel\Order $channelOrder): \M2E\Temu\Model\Order
    {
        $create = $this->createFactory->create(
            $this->account,
            $channelOrder
        );

        return $create->process();
    }

    private function update(\M2E\Temu\Model\Order $order, \M2E\Temu\Model\Channel\Order $channelOrder): void
    {
        $update = $this->updateFactory->create($order, $channelOrder);
        $update->process();
    }
}
