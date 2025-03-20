<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Listing\Wizard\Settings;

class View extends \M2E\Temu\Block\Adminhtml\Magento\AbstractContainer
{
    use \M2E\Temu\Block\Adminhtml\Listing\Wizard\WizardTrait;

    private \M2E\Temu\Model\Listing\Ui\RuntimeStorage $uiListingRuntimeStorage;
    private \M2E\Temu\Model\Listing\Wizard\Ui\RuntimeStorage $uiWizardRuntimeStorage;

    public function __construct(
        \M2E\Temu\Model\Listing\Ui\RuntimeStorage $uiListingRuntimeStorage,
        \M2E\Temu\Model\Listing\Wizard\Ui\RuntimeStorage $uiWizardRuntimeStorage,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Widget $context,
        array $data = []
    ) {
        $this->uiListingRuntimeStorage = $uiListingRuntimeStorage;
        $this->uiWizardRuntimeStorage = $uiWizardRuntimeStorage;

        parent::__construct($context, $data);
    }

    public function _construct(): void
    {
        parent::_construct();

        $this->setId('SettingsForListingProducts');

        $urlSave = $this->getUrl(
            '*/listing_wizard_settings/save',
            [
                'id' => $this->uiListingRuntimeStorage->getListing()->getId(),
                'wizard_id' => $this->getWizardIdFromRequest(),
            ]
        );

        $this->prepareButtons(
            [
                'class' => 'action-primary forward',
                'label' => __('Continue'),
                'onclick' => 'CommonObj.saveClick(\'' . $urlSave . '\')',
            ],
            $this->uiWizardRuntimeStorage->getManager()
        );
    }

    protected function _toHtml()
    {
        $settings = $this
            ->getLayout()
            ->createBlock(\M2E\Temu\Block\Adminhtml\Settings\Tabs\Main::class);

        $settings->toHtml();
        $form = $settings->getForm();

        $form->setData([
            'id' => 'edit_form',
            'method' => 'post',
        ]);

        $form->setUseContainer(true);

        return parent::_toHtml()
            . $form->toHtml();
    }
}
