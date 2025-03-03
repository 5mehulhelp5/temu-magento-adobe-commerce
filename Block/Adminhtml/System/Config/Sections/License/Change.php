<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\System\Config\Sections\License;

class Change extends \M2E\Temu\Block\Adminhtml\System\Config\Sections
{
    private \M2E\Core\Model\License $license;

    public function __construct(
        \M2E\Core\Model\License $license,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->license = $license;
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'method' => 'post',
                    'action' => 'javascript:void(0)',
                ],
            ]
        );

        $fieldSet = $form->addFieldset('change_license', ['legend' => '', 'collapsable' => false]);

        $key = \M2E\Core\Helper\Data::escapeHtml($this->license->getKey());
        $fieldSet->addField(
            'new_license_key',
            'text',
            [
                'name' => 'new_license_key',
                'label' => __('New License Key'),
                'title' => __('New License Key'),
                'value' => $key,
                'required' => true,
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
