<?php

namespace M2E\Temu\Block\Adminhtml\Wizard;

abstract class Installation extends AbstractWizard
{
    /** @var \M2E\Temu\Helper\Data */
    private $dataHelper;

    /**
     * @param \M2E\Temu\Helper\Data $dataHelper
     * @param \M2E\Temu\Helper\Module\Wizard $wizardHelper
     * @param \M2E\Temu\Block\Adminhtml\Magento\Context\Widget $context
     * @param array $data
     */
    public function __construct(
        \M2E\Temu\Helper\Data $dataHelper,
        \M2E\Temu\Helper\Module\Wizard $wizardHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Widget $context,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($dataHelper, $wizardHelper, $context, $data);
    }

    abstract protected function getStep();

    protected function _construct()
    {
        parent::_construct();

        $this->addButton(
            'continue',
            [
                'label' => __('Continue'),
                'class' => 'primary forward',
            ],
            1,
            0
        );
    }

    protected function _beforeToHtml()
    {
        $this->setId('wizard' . $this->getNick() . $this->getStep());

        return parent::_beforeToHtml();
    }

    protected function _toHtml()
    {
        $this->jsUrl->addUrls([
            'wizard_registration/createLicense' => $this->getUrl('*/wizard_registration/createLicense'),
            'wizard_installationTemu/beforeToken' => $this->getUrl('m2e_temu/wizard_installationTemu/beforeToken/'),
            'wizard_installationTemu/settingsContinue' => $this->getUrl('m2e_temu/wizard_installationTemu/settingsContinue/'),
        ]);

        $stepsBlock = $this->getLayout()->createBlock(
            $this->nameBuilder->buildClassName(
                [
                    '\M2E\Temu\Block\Adminhtml\Wizard',
                    $this->getNick(),
                    'Breadcrumb',
                ]
            )
        )->setSelectedStep($this->getStep());

        $helpBlock = $this->getLayout()
                          ->createBlock(\M2E\Temu\Block\Adminhtml\HelpBlock::class, 'wizard.help.block')
                          ->setData(
                              [
                                  'no_collapse' => true,
                                  'no_hide' => true,
                              ]
                          );

        $contentBlock = $this->getLayout()->createBlock(
            $this->nameBuilder->buildClassName(
                [
                    '\M2E\Temu\Block\Adminhtml\Wizard',
                    $this->getNick(),
                    'Installation',
                    $this->getStep(),
                    'Content',
                ]
            )
        )->setData(
            [
                'nick' => $this->getNick(),
            ]
        );

        return parent::_toHtml() .
            $stepsBlock->toHtml() .
            $helpBlock->toHtml() .
            $contentBlock->toHtml();
    }
}
