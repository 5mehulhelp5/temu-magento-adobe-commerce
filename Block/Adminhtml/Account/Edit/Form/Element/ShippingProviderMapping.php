<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Account\Edit\Form\Element;

class ShippingProviderMapping extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    public function getAccount(): \M2E\Temu\Model\Account
    {
        return $this->getData('account');
    }

    public function getExistShippingProviderMapping(): array
    {
        return (array)$this->getData('exist_shipping_provider_mapping');
    }
}
