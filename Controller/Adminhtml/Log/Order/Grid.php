<?php

namespace M2E\Temu\Controller\Adminhtml\Log\Order;

class Grid extends \M2E\Temu\Controller\Adminhtml\Log\AbstractOrder
{
    public function execute()
    {
        $response = $this->getLayout()
                         ->createBlock(\M2E\Temu\Block\Adminhtml\Log\Order\Grid::class)
                         ->toHtml();
        $this->setAjaxContent($response);

        return $this->getResult();
    }
}
