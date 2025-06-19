<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Dashboard;

class Index extends \M2E\Temu\Controller\Adminhtml\AbstractMain
{
    public function execute()
    {
        $this->getResultPage()
             ->getConfig()
             ->getTitle()
             ->prepend(__('Dashboard'));

        return $this->getResult();
    }
}
