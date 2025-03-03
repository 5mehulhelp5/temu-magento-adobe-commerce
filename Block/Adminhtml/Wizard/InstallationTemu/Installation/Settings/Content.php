<?php

namespace M2E\Temu\Block\Adminhtml\Wizard\InstallationTemu\Installation\Settings;

use M2E\Temu\Block\Adminhtml\Magento\Form\AbstractForm;

class Content extends AbstractForm
{
    private \M2E\Temu\Model\Account\Repository $accountRepository;
    private \M2E\Temu\Model\ShippingProvider\Repository $shippingProviderRepository;

    public function __construct(
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\ShippingProvider\Repository $shippingProviderRepository,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->accountRepository = $accountRepository;
        $this->shippingProviderRepository = $shippingProviderRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('wizardInstallationSettings');
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('wizard.help.block')->setContent(
            __(
                'In this section, you can configure the general settings for the interaction ' .
                'between %extension_title and %channel_title.<br><br>Anytime you can change these ' .
                'settings under <b>%channel_title > Configuration > General</b>.',
                [
                    'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
                    'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                ]
            )
        );

        parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $settings = $this
            ->getLayout()
            ->createBlock(\M2E\Temu\Block\Adminhtml\Settings\Tabs\Main::class);

        $settings->toHtml();
        $form = $settings->getForm();

        $this->addShippingMappingFieldset($form);

        $form->setData([
            'id' => 'edit_form',
            'method' => 'post',
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);
    }

    private function addShippingMappingFieldset(\Magento\Framework\Data\Form $form)
    {
        $account = $this->accountRepository->getFirst();
        $shippingProvider = $this->shippingProviderRepository->getByAccount($account);

        $fieldset = $form->addFieldset(
            'shipping_mapping',
            [
                'legend' => __('Shipping Mapping'),
                'collapsable' => false,
            ]
        );

        $fieldset->addField(
            'account_settings_account_id',
            'hidden',
            [
                'name' => 'account_settings[account_id]',
                'value' => $account->getId(),
            ]
        );

        if (empty($shippingProvider)) {
            $fieldset->addField(
                'shipping_mapping_note',
                'note',
                [
                    'text' => __(
                        "Available Shipping Carriers failed to download at this step.<br>"
                        . "To view and configure them, use \"Refresh Account Data\" after completing these steps."
                    ),
                ]
            );
        } else {
            $shippingMappingField = $fieldset->addField(
                'shipping_provider_mapping',
                \M2E\Temu\Block\Adminhtml\Account\Edit\Form\Element\ShippingProviderMapping::class,
                [
                    'account' => $account,
                    'exist_shipping_provider_mapping' => [],
                ]
            );

            /** @var \M2E\Temu\Block\Adminhtml\Account\Edit\Form\Render $render */
            $render = $this
                ->getLayout()
                ->createBlock(\M2E\Temu\Block\Adminhtml\Account\Edit\Form\Render::class);
            $shippingMappingField->setRenderer($render);
        }
    }
}
