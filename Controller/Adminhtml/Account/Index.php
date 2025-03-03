<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Account;

use M2E\Temu\Controller\Adminhtml\AbstractAccount;

class Index extends AbstractAccount
{
    public function execute()
    {
        $this->getResultPage()
             ->getConfig()
             ->getTitle()
             ->prepend(__('Accounts'));

        return $this->getResultPage();
    }
}
