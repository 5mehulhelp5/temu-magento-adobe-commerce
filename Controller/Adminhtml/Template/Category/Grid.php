<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Template\Category;

class Grid extends \M2E\Temu\Controller\Adminhtml\Template\AbstractCategory
{
    public function execute()
    {
        /** @var \M2E\Temu\Block\Adminhtml\Template\Category\Grid $grid */
        $grid = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Template\Category\Grid::class
        );

        $this->setAjaxContent($grid->toHtml());

        return $this->getResult();
    }
}
