<?php

namespace M2E\Temu\Block\Adminhtml\Wizard;

use M2E\Temu\Block\Adminhtml\Magento\AbstractBlock;

class Congratulation extends AbstractBlock
{
    protected function _toHtml()
    {
        $message = __(
            'Installation Wizard is completed. If you can\'t proceed, please contact us at <a href="mailto:%mail">%mail</a>.',
            ['mail' => 'support@m2epro.com']
        );

        return "<h2>$message</h2>";
    }
}
