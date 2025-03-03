<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Wizard\InstallationTemu;

class SettingsContinue extends Installation
{
    private \M2E\Temu\Model\Settings $settings;
    private \M2E\Temu\Model\Account\ShippingMappingUpdater $shippingMappingUpdater;

    public function __construct(
        \M2E\Temu\Model\Settings $settings,
        \M2E\Core\Helper\Magento $magentoHelper,
        \M2E\Temu\Helper\Module\Wizard $wizardHelper,
        \Magento\Framework\Code\NameBuilder $nameBuilder,
        \M2E\Core\Model\LicenseService $licenseService,
        \M2E\Temu\Model\Account\ShippingMappingUpdater $shippingMappingUpdater
    ) {
        parent::__construct(
            $magentoHelper,
            $wizardHelper,
            $nameBuilder,
            $licenseService,
        );
        $this->settings = $settings;
        $this->shippingMappingUpdater = $shippingMappingUpdater;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (empty($params)) {
            return $this->indexAction();
        }

        $this->settings->setConfigValues($params);

        $this->saveShippingMapping(
            (int)$params['account_settings']['account_id'],
            $params['shipping_provider_mapping'] ?? []
        );

        $this->setStep($this->getNextStep());

        return $this->_redirect('*/*/installation');
    }

    private function saveShippingMapping(int $accountId, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $this->shippingMappingUpdater->update($accountId, $data);
    }
}
