<?php

namespace M2E\Temu\Block\Adminhtml\Template\Edit;

class Form extends \M2E\Temu\Block\Adminhtml\Magento\Form\AbstractForm
{
    /** @var \M2E\Temu\Helper\Data\GlobalData */
    private $globalDataHelper;

    public function __construct(
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \M2E\Temu\Helper\Data\GlobalData $globalDataHelper,
        array $data = []
    ) {
        $this->globalDataHelper = $globalDataHelper;

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

        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }

    public function getTemplateData()
    {
        $nick = $this->getTemplateNick();
        $templateData = $this->globalDataHelper->getValue("temu_template_$nick");

        return array_merge([
            'title' => '',
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
