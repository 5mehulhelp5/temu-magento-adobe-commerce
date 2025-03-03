<?php

namespace M2E\Temu\Controller\Adminhtml\Order;

use M2E\Temu\Controller\Adminhtml\AbstractOrder;

class NoteGrid extends AbstractOrder
{
    public function execute()
    {
        $grid = $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Order\Note\Grid::class);
        $this->setAjaxContent($grid->toHtml());

        return $this->getResult();
    }
}
