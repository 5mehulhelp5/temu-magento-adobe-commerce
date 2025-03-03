<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Order\UploadByUser;

class GetPopupGrid extends \M2E\Temu\Controller\Adminhtml\AbstractOrder
{
    public function execute()
    {
        /** @var \M2E\Temu\Block\Adminhtml\Order\UploadByUser\Grid $block */
        $block = $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Order\UploadByUser\Grid::class);
        $this->setAjaxContent($block->toHtml());

        return $this->getResult();
    }
}
