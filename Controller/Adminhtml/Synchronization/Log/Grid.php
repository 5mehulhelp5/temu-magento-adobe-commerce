<?php

namespace M2E\Temu\Controller\Adminhtml\Synchronization\Log;

class Grid extends \M2E\Temu\Controller\Adminhtml\Synchronization\AbstractLog
{
    public function execute()
    {
        $this->setAjaxContent(
            $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Synchronization\Log\Grid::class)
        );

        return $this->getResult();
    }
}
