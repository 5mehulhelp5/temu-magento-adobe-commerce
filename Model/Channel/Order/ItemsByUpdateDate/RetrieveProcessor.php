<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Order\ItemsByUpdateDate;

class RetrieveProcessor
{
    private \M2E\Temu\Model\Connector\Client\Single $singleClient;

    public function __construct(\M2E\Temu\Model\Connector\Client\Single $singleClient)
    {
        $this->singleClient = $singleClient;
    }

    public function process(
        \M2E\Temu\Model\Account $account,
        \DateTimeInterface $updateFrom,
        \DateTimeInterface $updateTo
    ): \M2E\Temu\Model\Channel\Connector\Order\Get\Items\Response {
        $command = new \M2E\Temu\Model\Channel\Connector\Order\Get\ItemsByUpdateDateCommand(
            $account->getServerHash(),
            $updateFrom,
            $updateTo,
        );

        /** @var \M2E\Temu\Model\Channel\Connector\Order\Get\Items\Response */
        return $this->singleClient->process($command);
    }
}
