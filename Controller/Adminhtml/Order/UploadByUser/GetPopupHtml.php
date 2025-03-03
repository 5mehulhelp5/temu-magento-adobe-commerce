<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Order\UploadByUser;

class GetPopupHtml extends \M2E\Temu\Controller\Adminhtml\AbstractOrder
{
    public function execute()
    {
        /** @var \M2E\Temu\Block\Adminhtml\Order\UploadByUser\Popup $block */
        $block = $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Order\UploadByUser\Popup::class);
        $this->setAjaxContent($block->toHtml());

        return $this->getResult();
    }
}
