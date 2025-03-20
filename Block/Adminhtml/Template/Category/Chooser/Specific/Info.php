<?php

namespace M2E\Temu\Block\Adminhtml\Template\Category\Chooser\Specific;

class Info extends \M2E\Temu\Block\Adminhtml\Widget\Info
{
    protected function _prepareLayout()
    {
        $this->setInfo(
            [
                [
                    'label' => __('Category'),
                    'value' => $this->getData('path'),
                ],
            ]
        );

        return parent::_prepareLayout();
    }
}
