<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Account;

class AddAccountButtons implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    private \M2E\Temu\Model\Account\Ui\UrlHelper $accountUrlHelper;

    public function __construct(
        \M2E\Temu\Model\Account\Ui\UrlHelper $accountUrlHelper
    ) {
        $this->accountUrlHelper = $accountUrlHelper;
    }

    public function getButtonData()
    {
        return [
            'id' => 'add-temu-account',
            'label' => __('Add Account'),
            'class' => 'action-primary action-button',
            'style' => 'pointer-events: none',
            'class_name' => \M2E\Temu\Block\Adminhtml\Magento\Button\SplitButton::class,
            'options' => $this->getDropdownOptions(),
        ];
    }

    private function getDropdownOptions(): array
    {
        return [
            [
                'label' => 'US',
                'id' => 'US',
                'onclick' => 'setLocation(this.getAttribute("data-url"))',
                'data_attribute' => [
                    'url' => $this->accountUrlHelper->getBeforeGetTokenUrl(
                        ['_current' => true, 'region' => 'US']
                    ),
                ],
            ],
            [
                'label' => 'EU',
                'id' => 'EU',
                'onclick' => 'setLocation(this.getAttribute("data-url"))',
                'data_attribute' => [
                    'url' => $this->accountUrlHelper->getBeforeGetTokenUrl(
                        ['_current' => true, 'region' => 'EU']
                    ),
                ],
            ]
        ];
    }
}
