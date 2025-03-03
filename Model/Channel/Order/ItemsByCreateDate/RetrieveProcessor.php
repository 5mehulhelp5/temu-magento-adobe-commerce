<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Order\ItemsByCreateDate;

class RetrieveProcessor
{
    private \M2E\Temu\Model\Connector\Client\Single $singleClient;

    public function __construct(\M2E\Temu\Model\Connector\Client\Single $singleClient)
    {
        $this->singleClient = $singleClient;
    }

    public function process(
        \M2E\Temu\Model\Account $account,
        \DateTimeInterface $createFrom,
        \DateTimeInterface $createTo
    ): \M2E\Temu\Model\Channel\Connector\Order\Get\Items\Response {
        $command = new \M2E\Temu\Model\Channel\Connector\Order\Get\ItemsByCreateDateCommand(
            $account->getServerHash(),
            $createFrom,
            $createTo,
        );

        /** @var \M2E\Temu\Model\Channel\Connector\Order\Get\Items\Response */
        return $this->singleClient->process($command);
    }
}
