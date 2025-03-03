<?php

namespace M2E\Temu\Controller\Adminhtml\Order;

class Grid extends AbstractOrder
{
    public function execute()
    {
        /** @var \M2E\Temu\Block\Adminhtml\Order\Grid $grid */
        $grid = $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Order\Grid::class);

        $this->setAjaxContent($grid->toHtml());

        return $this->getResult();
    }
}
