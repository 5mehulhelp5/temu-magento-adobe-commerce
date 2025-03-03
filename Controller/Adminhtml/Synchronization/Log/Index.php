<?php

namespace M2E\Temu\Controller\Adminhtml\Synchronization\Log;

class Index extends \M2E\Temu\Controller\Adminhtml\Synchronization\AbstractLog
{
    public function execute()
    {
        $this->addContent(
            $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Synchronization\Log::class)
        );
        $this->getResultPage()->getConfig()->getTitle()->prepend(__('Synchronization Logs'));

        return $this->getResult();
    }
}
