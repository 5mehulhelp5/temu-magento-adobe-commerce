<?php

namespace M2E\Temu\Block\Adminhtml\Template\Synchronization\Edit\Form\Tabs;

use M2E\Temu\Block\Adminhtml\Magento\Form\AbstractForm;

abstract class AbstractTab extends AbstractForm
{
    /** @var \M2E\Temu\Helper\Data\GlobalData */
    protected $globalDataHelper;

    public function __construct(
        \M2E\Temu\Helper\Data\GlobalData $globalDataHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->globalDataHelper = $globalDataHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function isCustom()
    {
        $isCustom = $this->globalDataHelper->getValue('is_custom');
        if ($isCustom !== null) {
            return (bool)$isCustom;
        }

        return false;
    }

    public function getTitle()
    {
        if ($this->isCustom()) {
            $customTitle = $this->globalDataHelper->getValue('custom_title');

            return $customTitle !== null ? $customTitle : '';
        }

        $template = $this->globalDataHelper->getValue('temu_template_synchronization');

        if ($template === null) {
            return '';
        }

        return $template->getTitle();
    }

    public function getFormData()
    {
        $template = $this->globalDataHelper->getValue('temu_template_synchronization');

        if ($template === null || $template->getId() === null) {
            return [];
        }

        return $template->getData();
    }
}
