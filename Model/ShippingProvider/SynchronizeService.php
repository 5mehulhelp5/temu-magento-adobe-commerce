<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ShippingProvider;

class SynchronizeService
{
    private \M2E\Temu\Model\Connector\Client\Single $singleClient;
    private \M2E\Temu\Model\ShippingProviderFactory $shippingProviderFactory;
    private \M2E\Temu\Model\ShippingProvider\Repository $shippingProviderRepository;

    public function __construct(
        \M2E\Temu\Model\Connector\Client\Single $singleClient,
        \M2E\Temu\Model\ShippingProviderFactory $shippingProviderFactory,
        \M2E\Temu\Model\ShippingProvider\Repository $shippingProviderRepository
    ) {
        $this->singleClient = $singleClient;
        $this->shippingProviderFactory = $shippingProviderFactory;
        $this->shippingProviderRepository = $shippingProviderRepository;
    }

    public function synchronizeShippingProviders(\M2E\Temu\Model\Account $account): void
    {
        $shippingProviders = $this->receiveShippingProviders($account);

        $providers = [];
        $existedShippingProviders = $this->shippingProviderRepository->getByAccount($account);

        foreach ($shippingProviders->getShippingProviders() as $shippingProvider) {
            $entity = $this->shippingProviderFactory->create();
            $entity->create(
                $account,
                $shippingProvider->getId(),
                $shippingProvider->getName(),
                $shippingProvider->getRegion()->getId(),
                $shippingProvider->getRegion()->getName(),
            );

            $providers[$shippingProvider->getId()] = $entity;
            $existedProvider = $this->shippingProviderRepository->findExistedShippingProvider($entity);

            if ($existedProvider === null) {
                $this->shippingProviderRepository->create($entity);
                continue;
            }

            if ($existedProvider->getShippingProviderName() !== $shippingProvider->getName()) {
                $existedProvider->setShippingProviderName($shippingProvider->getName());
                $this->shippingProviderRepository->save($existedProvider);
            }

            if ($existedProvider->getShippingProviderRegionId() !== $shippingProvider->getRegion()->getId()) {
                $existedProvider->setShippingProviderRegionId($shippingProvider->getRegion()->getId());
                $existedProvider->setShippingProviderRegionName($shippingProvider->getRegion()->getName());
                $this->shippingProviderRepository->save($existedProvider);
            }
        }

        $this->removeNotExistedShippingProviders($existedShippingProviders, $providers);
    }

    /**
     * @param \M2E\Temu\Model\ShippingProvider[] $extensionShippingProviders
     * @param array<string,\M2E\Temu\Model\ShippingProvider> $chanelShippingProviders
     */
    private function removeNotExistedShippingProviders(array $extensionShippingProviders, array $chanelShippingProviders): void
    {
        if (empty($extensionShippingProviders)) {
            return;
        }

        foreach ($extensionShippingProviders as $shippingProvider) {
            if (isset($chanelShippingProviders[$shippingProvider->getShippingProviderId()])) {
                continue;
            }

            $this->shippingProviderRepository->delete($shippingProvider);
        }
    }

    /**
     * @param \M2E\Temu\Model\Account $account
     *
     * @return \M2E\Temu\Model\Channel\Connector\Shipping\GetProviders\Response
     * @throws \M2E\Core\Model\Exception
     * @throws \M2E\Core\Model\Exception\Connection
     */
    private function receiveShippingProviders(
        \M2E\Temu\Model\Account $account
    ): \M2E\Temu\Model\Channel\Connector\Shipping\GetProviders\Response {
        $command = new \M2E\Temu\Model\Channel\Connector\Shipping\GetProviders(
            $account->getServerHash()
        );

        /** @var \M2E\Temu\Model\Channel\Connector\Shipping\GetProviders\Response $response */
        $response = $this->singleClient->process($command);

        return $response;
    }
}
