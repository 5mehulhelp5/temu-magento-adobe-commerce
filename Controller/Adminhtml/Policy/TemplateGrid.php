<?php

namespace M2E\Temu\Controller\Adminhtml\Policy;

use M2E\Temu\Controller\Adminhtml\AbstractTemplate;

class TemplateGrid extends AbstractTemplate
{
    public function execute()
    {
        /** @var \M2E\Temu\Block\Adminhtml\Template\Grid $switcherBlock */
        $grid = $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Template\Grid::class);

        $this->setAjaxContent($grid->toHtml());

        return $this->getResult();
    }
}
