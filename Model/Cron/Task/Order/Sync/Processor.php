<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Cron\Task\Order\Sync;

use M2E\Temu\Model\Channel\Order\ItemsByUpdateDate\RetrieveProcessor as ItemsByUpdateDateProcessor;

class Processor
{
    private \M2E\Temu\Model\Synchronization\LogService $synchronizationLog;
    private ItemsByUpdateDateProcessor $receiveOrdersProcessor;
    private \M2E\Temu\Model\Account $account;
    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Model\Order\MagentoProcessor $orderMagentoProcessor;
    private \M2E\Temu\Model\Order\UpdateFromChannelFactory $updateFromChannelFactory;

    public function __construct(
        \M2E\Temu\Model\Account $account,
        \M2E\Temu\Model\Order\MagentoProcessor $orderMagentoProcessor,
        \M2E\Temu\Model\Synchronization\LogService $logService,
        ItemsByUpdateDateProcessor $receiveOrdersProcessor,
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\Order\UpdateFromChannelFactory $updateFromChannelFactory
    ) {
        $this->receiveOrdersProcessor = $receiveOrdersProcessor;
        $this->synchronizationLog = $logService;
        $this->account = $account;
        $this->accountRepository = $accountRepository;
        $this->orderMagentoProcessor = $orderMagentoProcessor;
        $this->updateFromChannelFactory = $updateFromChannelFactory;
    }

    public function process(): void
    {
        $toTime = \M2E\Core\Helper\Date::createImmutableCurrentGmt();
        $fromTime = $this->prepareFromTime($toTime);

        $response = $this->receiveOrdersProcessor->process(
            $this->account,
            $fromTime,
            $toTime
        );

        $this->processResponseMessages($response->getMessageCollection());

        $this->updateLastOrderSynchronizationDate($this->account, $response->getMaxDateInResult());

        if (empty($response->getOrders())) {
            return;
        }

        $ordersCreator = $this->updateFromChannelFactory->create($this->account, true);

        $orders = $ordersCreator->process($response->getOrders());

        $this->orderMagentoProcessor->processBatch(
            $orders,
            \M2E\Core\Helper\Data::INITIATOR_EXTENSION,
            true,
            true
        );
    }

    // ---------------------------------------

    private function processResponseMessages(
        \M2E\Core\Model\Connector\Response\MessageCollection $messageCollection
    ): void {
        foreach ($messageCollection->getMessages() as $message) {
            if (!$message->isError() && !$message->isWarning()) {
                continue;
            }

            $logType = $message->isError()
                ? \M2E\Temu\Model\Log\AbstractModel::TYPE_ERROR
                : \M2E\Temu\Model\Log\AbstractModel::TYPE_WARNING;

            $this->synchronizationLog->add((string)__($message->getText()), $logType);
        }
    }

    private function prepareFromTime(
        \DateTimeImmutable $toTime
    ): \DateTimeImmutable {
        $lastSynchronizationDate = $this->account->getOrdersLastSyncDate();

        if ($lastSynchronizationDate === null) {
            $sinceTime = \M2E\Core\Helper\Date::createImmutableCurrentGmt();
        } else {
            $sinceTime = $lastSynchronizationDate;

            // Get min date for sync
            // ---------------------------------------
            $minDate = \M2E\Core\Helper\Date::createImmutableCurrentGmt()->modify('-90 days');
            // ---------------------------------------

            // Prepare last date
            // ---------------------------------------
            if ($sinceTime->getTimestamp() < $minDate->getTimestamp()) {
                $sinceTime = $minDate;
            }
        }

        if ($sinceTime->getTimestamp() >= $toTime->getTimeStamp()) {
            $sinceTime = $toTime->modify('- 5 minutes');
        }

        return $sinceTime;
    }

    private function updateLastOrderSynchronizationDate(
        \M2E\Temu\Model\Account $account,
        \DateTimeInterface $date
    ): void {
        $account->setOrdersLastSyncDate($date);

        $this->accountRepository->save($account);
    }
}
