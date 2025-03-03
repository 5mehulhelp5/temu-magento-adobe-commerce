<?php

declare(strict_types=1);

namespace M2E\Temu\Ui\Product\Component\Unmanaged\Column;

use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Price extends Column
{
    private CurrencyInterface $localeCurrency;
    private \M2E\Temu\Model\UnmanagedProduct\Repository $unmanagedRepository;
    private \M2E\Temu\Model\Account\Repository $accountRepository;

    public function __construct(
        \M2E\Temu\Model\UnmanagedProduct\Repository $unmanagedRepository,
        \M2E\Temu\Model\Account\Repository $accountRepository,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CurrencyInterface $localeCurrency,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->localeCurrency = $localeCurrency;
        $this->unmanagedRepository = $unmanagedRepository;
        $this->accountRepository = $accountRepository;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$row) {
            try {
                $unmanagedProduct = $this->unmanagedRepository->get((int)$row['id']);
                $account = $this->accountRepository->get($unmanagedProduct->getAccountId());
                $currencyCode = $account->getCurrencyCode();
            } catch (\M2E\Temu\Model\Exception\Logic $e) {
                continue;
            }

            $isSimple = (int)$row['is_simple'] === 1;

            if ($isSimple) {
                $price = $unmanagedProduct->getPrice();
                if (empty($price)) {
                    $row['price'] = __('N/A');

                    continue;
                }

                if ($price <= 0) {
                    $row['price'] = '<span style="color: #f00;">0</span>';

                    continue;
                }

                $row['price'] = $this->localeCurrency
                    ->getCurrency($currencyCode)
                    ->toCurrency($price);

                continue;
            }

            $onlineMinPrice = $row['min_price'];
            $onlineMaxPrice = $row['max_price'];

            if ($onlineMinPrice === null && $onlineMaxPrice === null) {
                $row['price'] = __('N/A');

                continue;
            }

            if (
                (!empty($onlineMinPrice) && empty($onlineMaxPrice))
                || $onlineMinPrice === $onlineMaxPrice
            ) {
                $row['price'] = $this->localeCurrency
                    ->getCurrency($currencyCode)
                    ->toCurrency($onlineMinPrice);

                continue;
            }

            if (
                $onlineMaxPrice !== null
                && $onlineMinPrice === null
            ) {
                $row['price'] = $this->localeCurrency
                    ->getCurrency($currencyCode)
                    ->toCurrency($onlineMaxPrice);

                continue;
            }

            $formattedMinPrice = $this->localeCurrency
                ->getCurrency($currencyCode)
                ->toCurrency($onlineMinPrice);

            $formattedMaxPrice = $this->localeCurrency
                ->getCurrency($currencyCode)
                ->toCurrency($onlineMaxPrice);

            $row['price'] = sprintf('%s - %s', $formattedMinPrice, $formattedMaxPrice);
        }

        return $dataSource;
    }
}
