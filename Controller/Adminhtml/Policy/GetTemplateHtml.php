<?php

namespace M2E\Temu\Controller\Adminhtml\Policy;

use M2E\Temu\Controller\Adminhtml\AbstractTemplate;

class GetTemplateHtml extends AbstractTemplate
{
    private \M2E\Temu\Helper\Component\Temu\Template\Switcher\DataLoader $templateSwitcherDataLoader;

    public function __construct(
        \M2E\Temu\Helper\Component\Temu\Template\Switcher\DataLoader $templateSwitcherDataLoader,
        \M2E\Temu\Model\Policy\Manager $templateManager
    ) {
        parent::__construct($templateManager);

        $this->templateSwitcherDataLoader = $templateSwitcherDataLoader;
    }

    public function execute()
    {
        try {
            // ---------------------------------------
            $dataLoader = $this->templateSwitcherDataLoader;
            $dataLoader->load($this->getRequest());
            // ---------------------------------------

            // ---------------------------------------
            $templateNick = $this->getRequest()->getParam('nick');
            $templateDataForce = (bool)$this->getRequest()->getParam('data_force', false);

            /** @var \M2E\Temu\Block\Adminhtml\Listing\Template\Switcher $switcherBlock */
            $switcherBlock = $this
                ->getLayout()
                ->createBlock(
                    \M2E\Temu\Block\Adminhtml\Listing\Template\Switcher::class
                );
            $switcherBlock->setData(['template_nick' => $templateNick]);
            // ---------------------------------------

            $this->setAjaxContent($switcherBlock->getFormDataBlockHtml($templateDataForce));
        } catch (\Exception $e) {
            $this->setJsonContent(['error' => $e->getMessage()]);
        }

        return $this->getResult();
    }
}
