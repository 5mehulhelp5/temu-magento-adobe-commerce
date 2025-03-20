<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Listing\Wizard\Settings;

use M2E\Temu\Model\Listing;

class Save extends \M2E\Temu\Controller\Adminhtml\AbstractListing
{
    private \M2E\Temu\Model\Settings $settings;

    public function __construct(
        \M2E\Temu\Model\Settings $settings
    ) {
        parent::__construct();

        $this->settings = $settings;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('M2E_Temu::listings_items');
    }

    public function execute()
    {
        if (!$post = $this->getRequest()->getParams()) {
            $this->_redirect('*/listing/index');
        }

        $wizardId = $this->getRequest()->getParam('wizard_id');

        $this->settings->setConfigValues($this->getRequest()->getParams());

        $urlCompleteStep = $this->getUrl(
            '*/listing_wizard_settings/completeStep',
            ['id' => $wizardId]
        );

        return $this->_redirect($urlCompleteStep);
    }
}
