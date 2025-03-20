<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Channel\Policy\Shipping;

class TemplateService
{
    private \M2E\Temu\Model\Connector\Client\Single $serverClient;

    public function __construct(
        \M2E\Temu\Model\Connector\Client\Single $serverClient
    ) {
        $this->serverClient = $serverClient;
    }

    /**
     * @param \M2E\Temu\Model\Account $account
     *
     * @return \M2E\Temu\Model\Channel\Policy\Shipping\Template\Collection
     * @throws \M2E\Temu\Model\Exception
     */
    public function retrieve(
        \M2E\Temu\Model\Account $account
    ): \M2E\Temu\Model\Channel\Policy\Shipping\Template\Collection {
        $command = new \M2E\Temu\Model\Channel\Connector\Policy\Shipping\Template\GetCommand(
            $account->getServerHash()
        );
        /** @var \M2E\Temu\Model\Channel\Policy\Shipping\Template\Collection $channelDeliveryTemplates */
        $channelDeliveryTemplates = $this->serverClient->process($command);

        return $channelDeliveryTemplates;
    }
}
