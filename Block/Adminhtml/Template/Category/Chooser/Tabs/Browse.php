<?php

namespace M2E\Temu\Block\Adminhtml\Template\Category\Chooser\Tabs;

class Browse extends \M2E\Temu\Block\Adminhtml\Magento\AbstractBlock
{
    public \M2E\Temu\Helper\View\Temu $viewHelper;
    private \M2E\Temu\Helper\Module\Wizard $wizardHelper;

    public function __construct(
        \M2E\Temu\Helper\View\Temu $viewHelper,
        \M2E\Temu\Helper\Module\Wizard $wizardHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->viewHelper = $viewHelper;
        $this->wizardHelper = $wizardHelper;
    }

    public function _construct()
    {
        parent::_construct();

        $this->setId('temuCategoryChooserCategoryBrowse');
        $this->setTemplate('template/category/chooser/tabs/browse.phtml');
    }

    public function isWizardActive()
    {
        return $this->wizardHelper->isActive(\M2E\Temu\Helper\View\Temu::WIZARD_INSTALLATION_NICK);
    }
}
