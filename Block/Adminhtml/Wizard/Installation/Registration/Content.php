<?php

namespace M2E\Temu\Block\Adminhtml\Wizard\Installation\Registration;

use M2E\Temu\Block\Adminhtml\Magento\Form\AbstractForm;

abstract class Content extends AbstractForm
{
    private \M2E\Core\Block\Adminhtml\RegistrationForm $form;

    public function __construct(
        \M2E\Core\Block\Adminhtml\RegistrationForm $form,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->form = $form;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('wizard.help.block')->setContent(
            (string)__(
                '%extension_title requires activation for further work. To activate your installation,
you should obtain a <strong>License Key</strong>. For more details, please read our
<a href="%privacy_url" target="_blank">Privacy Policy</a>.<br/><br/>
Fill out the form below with the required information. This information will be used to register
you on <a href="%accounts_url" target="_blank">M2E Accounts</a> and auto-generate a new License Key.<br/><br/>
Access to <a href="%accounts_url" target="_blank">M2E Accounts</a> will allow you to manage your Subscription, keep track
of your Trial and Paid terms, control your License Key details, and more.',
                [
                    'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                    'privacy_url' => \M2E\Core\Helper\Module\Support::WEBSITE_PRIVACY_URL,
                    'accounts_url' => \M2E\Core\Helper\Module\Support::ACCOUNTS_URL,
                ]
            )
        );

        parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = $this->form->getUserForm();
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
