<?php

namespace M2E\Temu\Block\Adminhtml\Account\Edit\Tabs;

class General extends \M2E\Temu\Block\Adminhtml\Magento\Form\AbstractForm
{
    private ?\M2E\Temu\Model\Account $account;
    private \M2E\Temu\Model\Account\Ui\UrlHelper $accountUrlHelper;

    public function __construct(
        \M2E\Temu\Model\Account\Ui\UrlHelper $accountUrlHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        ?\M2E\Temu\Model\Account $account = null,
        array $data = []
    ) {
        $this->account = $account;
        $this->accountUrlHelper = $accountUrlHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    // ----------------------------------------

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();

        $content = __(
            'This Page shows the Environment for your %channel_title Account and details of the ' .
            'authorisation for %extension_title to connect to your %channel_title Account.<br/><br/> If your token has ' .
            'expired or is not activated, click <b>Get Token</b>.<br/><br/>',
            [
                'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
            ]
        );

        $form->addField(
            'temu_accounts_general',
            self::HELP_BLOCK,
            [
                'content' => $content,
            ],
        );

        if ($this->account !== null) {
            $fieldset = $form->addFieldset(
                'general',
                [
                    'legend' => __('General'),
                    'collapsable' => false,
                ],
            );

            $fieldset->addField(
                'title',
                'text',
                [
                    'name' => 'title',
                    'class' => 'Temu-account-title',
                    'label' => __('Title'),
                    'value' => $this->account->getTitle(),
                    'tooltip' => __(
                        'Title or Identifier of %channel_title Account for your internal use.',
                        [
                            'channel_title' => \M2E\Temu\Helper\Module::getChannelTitle(),
                        ]
                    ),
                ],
            );
        }

        $fieldset = $form->addFieldset(
            'access_details',
            [
                'legend' => __('Access Details'),
                'collapsable' => false,
            ],
        );

        $button = $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Magento\Button::class)->addData(
            [
                'label' => __('Update Access Data'),
                'onclick' => sprintf(
                    'TemuAccountObj.get_token(\'%s\')',
                    $this->accountUrlHelper->getBeforeGetTokenUrl(
                        [
                            'id' => (int)$this->getRequest()->getParam('id'),
                        ]
                    )
                ),
                'class' => 'check temu_check_button primary',
            ],
        );

        $fieldset->addField(
            'update_access_data_container',
            'label',
            [
                'label' => '',
                'after_element_html' => $button->toHtml(),
            ],
        );

        $this->setForm($form);

        $id = $this->getRequest()->getParam('id');
        $this->js->add("Temu.formData.id = '$id';");

        $this->js->add(
            <<<JS
    require([
        'Temu/Account'
    ], function(){
        window.TemuAccountObj = new TemuAccount();
        TemuAccountObj.initObservers();
    });
JS,
        );

        return parent::_prepareForm();
    }
}
