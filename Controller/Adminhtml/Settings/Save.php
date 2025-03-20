<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Settings;

use M2E\Temu\Controller\Adminhtml\AbstractSettings;

class Save extends AbstractSettings
{
    private \M2E\Temu\Model\Settings $settings;

    public function __construct(
        \M2E\Temu\Model\Settings $settings
    ) {
        parent::__construct();

        $this->settings = $settings;
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->setJsonContent(['success' => false]);

            return $this->getResult();
        }

        $this->settings->setConfigValues($this->getRequest()->getParams());

        $this->setJsonContent(['success' => true]);

        return $this->getResult();
    }
}
