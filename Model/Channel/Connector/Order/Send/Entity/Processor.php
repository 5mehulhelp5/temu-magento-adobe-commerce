<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Connector\Order\Send\Entity;

class Processor
{
    private \M2E\Temu\Model\Connector\Client\Single $singleClient;

    public function __construct(\M2E\Temu\Model\Connector\Client\Single $singleClient)
    {
        $this->singleClient = $singleClient;
    }

    public function process(
        \M2E\Temu\Model\Account $account,
        string $trackingNumber,
        int $shippingProviderId,
        \M2E\Temu\Model\Channel\Connector\Order\Send\Entity\Order $order
    ): \M2E\Temu\Model\Channel\Connector\Order\Send\Entity\Response {
        $command = new \M2E\Temu\Model\Channel\Connector\Order\Send\Entity\Command(
            $account->getServerHash(),
            $order,
            $trackingNumber,
            $shippingProviderId
        );

        /** @var \M2E\Temu\Model\Channel\Connector\Order\Send\Entity\Response */
        return $this->singleClient->process($command);
    }
}
