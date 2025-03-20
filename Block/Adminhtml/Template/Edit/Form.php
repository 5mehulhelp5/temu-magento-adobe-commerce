<?php

namespace M2E\Temu\Block\Adminhtml\Template\Edit;

class Form extends \M2E\Temu\Block\Adminhtml\Magento\Form\AbstractForm
{
    /** @var \M2E\Temu\Helper\Data\GlobalData */
    private $globalDataHelper;
    private \M2E\Temu\Model\Account\Repository $accountRepository;

    public function __construct(
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \M2E\Temu\Helper\Data\GlobalData $globalDataHelper,
        array $data = []
    ) {
        $this->globalDataHelper = $globalDataHelper;
        $this->accountRepository = $accountRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function _construct()
    {
        parent::_construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('temuTemplateEditForm');
        // ---------------------------------------

        $this->css->addFile('temu/template.css');
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'edit_form',
                'action' => 'javascript:void(0)',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ],
        ]);

        $fieldset = $form->addFieldset(
            'general_fieldset',
            ['legend' => __('General'), 'collapsable' => false]
        );

        $templateData = $this->getTemplateData();

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'value' => $templateData['title'],
                'class' => 'input-text validate-title-uniqueness',
                'required' => true,
            ]
        );

        $templateNick = $this->getTemplateNick();

        if ($templateNick === \M2E\Temu\Model\Policy\Manager::TEMPLATE_SHIPPING) {
            if ($this->getRequest()->getParam('account_id', false) !== false) {
                $fieldset->addField(
                    'account_id_hidden',
                    'hidden',
                    [
                        'name' => 'shipping[account_id]',
                        'value' => $templateData['account_id'],
                    ]
                );
            }

            $fieldset->addField(
                'account_id',
                'select',
                [
                    'name' => 'shipping[account_id]',
                    'label' => __('Account'),
                    'title' => __('Account'),
                    'values' => $this->getAccountOptions(),
                    'value' => $templateData['account_id'],
                    'required' => true,
                    'disabled' => !empty($templateData['account_id']),
                ]
            );
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }

    public function getTemplateData()
    {
        $accountId = $this->getRequest()->getParam('account_id', false);

        $nick = $this->getTemplateNick();
        $templateData = $this->globalDataHelper->getValue("temu_template_$nick");

        return array_merge([
            'title' => '',
            'account_id' => ($accountId !== false) ? $accountId : ''
        ], $templateData->getData());
    }

    public function getTemplateNick()
    {
        return $this->getParentBlock()->getTemplateNick();
    }

    public function getTemplateId()
    {
        $template = $this->getParentBlock()->getTemplateObject();

        return $template ? $template->getId() : null;
    }

    private function getAccountOptions(): array
    {
        return $this->formatAccountOptions($this->accountRepository->getAll());
    }

    private function formatAccountOptions(array $accounts): array
    {
        $optionsResult = [
            ['value' => '', 'label' => ''],
        ];
        foreach ($accounts as $account) {
            $optionsResult[] = [
                'value' => $account->getId(),
                'label' => $account->getTitle(),
            ];
        }

        return $optionsResult;
    }

    protected function _toHtml()
    {
        $nick = $this->getTemplateNick();
        $this->jsUrl->addUrls([
            'policy/getTemplateHtml' => $this->getUrl(
                '*/policy/getTemplateHtml',
                [
                    'account_id' => null,
                    'id' => $this->getTemplateId(),
                    'nick' => $nick,
                    'mode' => \M2E\Temu\Model\Policy\Manager::MODE_TEMPLATE,
                    'data_force' => true,
                ]
            ),
            'policy/isTitleUnique' => $this->getUrl(
                '*/policy/isTitleUnique',
                [
                    'id' => $this->getTemplateId(),
                    'nick' => $nick,
                ]
            ),
            'deleteAction' => $this->getUrl(
                '*/policy/delete',
                [
                    'id' => $this->getTemplateId(),
                    'nick' => $nick,
                ]
            ),
        ]);

        $this->jsTranslator->addTranslations([
            'Policy Title is not unique.' => __('Policy Title is not unique.'),
            'Do not show any more' => __('Do not show this message anymore'),
            'Save Policy' => __('Save Policy'),
        ]);

        $this->js->addRequireJs(
            [
                'form' => 'Temu/Template/Edit/Form',
                'jquery' => 'jquery',
            ],
            <<<JS

        window.TemuTemplateEditObj = new TemuTemplateEdit();
        TemuTemplateEditObj.templateNick = '{$this->getTemplateNick()}';
        TemuTemplateEditObj.initObservers();
JS
        );

        return parent::_toHtml();
    }
}
