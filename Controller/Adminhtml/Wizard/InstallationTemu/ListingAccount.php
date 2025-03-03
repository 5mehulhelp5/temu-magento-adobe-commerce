<?php

namespace M2E\Temu\Controller\Adminhtml\Wizard\InstallationTemu;

class ListingAccount extends Installation
{
    public function execute()
    {
        return $this->_redirect('*/listing_create', ['step' => 1, 'wizard' => true, 'clear' => true]);
    }
}
