<?php

namespace M2E\Temu\Block\Adminhtml\Log\Listing\Product\View;

use M2E\Temu\Block\Adminhtml\Log\Listing\Product\AbstractView;

class View extends AbstractView
{
    protected function _toHtml()
    {
        $message = (string)__(
            'This Log contains information about the actions applied to ' .
            '%extension_title Listings and related Items.',
            [
                'extension_title' => \M2E\Temu\Helper\Module::getExtensionTitle(),
            ]
        );
        $helpBlock = $this
            ->getLayout()
            ->createBlock(\M2E\Temu\Block\Adminhtml\HelpBlock::class)
            ->setData([
                'content' => $message,
            ]);

        return $helpBlock->toHtml() . parent::_toHtml();
    }
}
