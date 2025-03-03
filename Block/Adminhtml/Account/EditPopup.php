<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Account;

class EditPopup extends \M2E\Temu\Block\Adminhtml\Magento\AbstractBlock
{
    private \Magento\Framework\View\Page\Config $config;
    private \M2E\Temu\Block\Adminhtml\Account\CredentialsFormFactory $credentialsFormFactory;

    public function __construct(
        \Magento\Framework\View\Page\Config $config,
        \M2E\Temu\Block\Adminhtml\Account\CredentialsFormFactory $credentialsFormFactory,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        array $data = []
    ) {
        $this->config = $config;
        $this->credentialsFormFactory = $credentialsFormFactory;

        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->config->addPageAsset('M2E_Temu::css/account/credentials.css');
    }

    protected function _prepareLayout()
    {
        $this->addChild('form', \M2E\Temu\Block\Adminhtml\Account\Edit\Form::class);

        return parent::_prepareLayout();
    }
}
