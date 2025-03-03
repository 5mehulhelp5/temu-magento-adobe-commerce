<?php

namespace M2E\Temu\Block\Adminhtml\Template\SellingFormat;

class Messages extends \M2E\Temu\Block\Adminhtml\Template\AbstractMessages
{
    private \M2E\Core\Helper\Magento\Store $magentoStoreHelper;
    private \M2E\Temu\Model\Currency $currency;
    private \Magento\Store\Model\Store $store;
    private \M2E\Temu\Model\Account $account;

    public function __construct(
        \M2E\Temu\Model\Currency $currency,
        \M2E\Core\Helper\Magento\Store $magentoStoreHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Store\Model\Store $store,
        \M2E\Temu\Model\Account $account,
        array $data = []
    ) {
        $this->store = $store;
        $this->currency = $currency;
        $this->magentoStoreHelper = $magentoStoreHelper;
        $this->account = $account;

        parent::__construct($context, $data);
    }

    public function getMessages(): array
    {
        $message = $this->getCurrencyConversionMessage();

        return ($message !== null) ? [$message] : [];
    }

    private function getCurrencyConversionMessage(): ?string
    {
        if (!$this->account->getId()) {
            return null;
        }

        $currencyCode = $this->account->getCurrencyCode();
        if (!$this->canDisplayCurrencyConversionMessage($currencyCode)) {
            return null;
        }

        $storePath = $this->magentoStoreHelper->getStorePath($this->store->getId());
        $allowed = $this->currency->isAllowed($currencyCode, $this->store);

        if (!$allowed) {
            $currencySetupUrl = $this->getUrl(
                'admin/system_config/edit',
                [
                    'section' => 'currency',
                    'website' => $this->store->getId() != \Magento\Store\Model\Store::DEFAULT_STORE_ID ?
                        $this->store->getWebsite()->getId() : null,
                    'store' => $this->store->getId() != \Magento\Store\Model\Store::DEFAULT_STORE_ID ?
                        $this->store->getId() : null,
                ]
            );

            return
                __(
                    'Currency "%currency_code" is not allowed in <a href="%url" target="_blank">Currency Setup</a> '
                    . 'for Store View "%store_path" of your Magento. '
                    . 'Currency conversion will not be performed.',
                    [
                        'currency_code' => $currencyCode,
                        'url' => $currencySetupUrl,
                        'store_path' => $this->escapeHtml($storePath)
                    ]
                );
        }

        $rate = $this->currency->getConvertRateFromBase($currencyCode, $this->store);

        if ($rate == 0) {
            return
                __(
                    'There is no rate for "%currency_from-%currency_to" in'
                    . ' <a href="%url" target="_blank">Manage Currency Rates</a> of your Magento.'
                    . ' Currency conversion will not be performed.',
                    [
                        'currency_from' => $this->store->getBaseCurrencyCode(),
                        'currency_to' => $currencyCode,
                        'url' => $this->getUrl('adminhtml/system_currency'),
                    ]
                );
        }

        $message =
            $this->__(
                'There is a rate %rate for "%currency_from-%currency_to" in'
                . ' <a href="%url" target="_blank">Manage Currency Rates</a> of your Magento.'
                . ' Currency conversion will be performed automatically.',
                [
                    'rate' => $rate,
                    'currency_from' => $this->store->getBaseCurrencyCode(),
                    'currency_to' => $currencyCode,
                    'url' => $this->getUrl('adminhtml/system_currency'),
                ]
            );

        return '<span style="color: #3D6611 !important;">' . $message . '</span>';
    }

    private function canDisplayCurrencyConversionMessage(string $currencyCode): bool
    {
        if ($this->store->getId() === null) {
            return false;
        }

        if ($this->currency->isBase($currencyCode, $this->store)) {
            return false;
        }

        return true;
    }
}
