<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Account;

class ShippingMappingUpdater
{
    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Model\Account\ShippingMappingFactory $shippingMappingFactory;

    public function __construct(
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\Account\ShippingMappingFactory $shippingMappingFactory
    ) {
        $this->accountRepository = $accountRepository;
        $this->shippingMappingFactory = $shippingMappingFactory;
    }

    public function update(int $accountId, array $data): void
    {
        $preparedData = $this->prepareShippingProviderData($data);
        $account = $this->accountRepository->find($accountId);

        if ($account === null) {
            return;
        }

        $account->setShippingProviderMapping($preparedData);
        $this->accountRepository->save($account);
    }

    public function prepareShippingProviderData(array $data): \M2E\Temu\Model\Account\ShippingMapping
    {
        foreach ($data as $regionId => $shippingProviderData) {
            foreach ($shippingProviderData as $carrierCode => $shippingProviderId) {
                if (empty($shippingProviderId)) {
                    unset($data[$regionId][$carrierCode]);
                }
            }
        }

        return $this->shippingMappingFactory->create($data);
    }
}
