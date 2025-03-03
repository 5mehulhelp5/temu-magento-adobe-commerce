<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Listing\Unmanaged;

class ButtonsBuilder extends \M2E\Temu\Block\Adminhtml\Magento\AbstractContainer
{
    public function _construct(): void
    {
        parent::_construct();

        $this->addButton('buttons_block', ['class_name' => ButtonsBlock::class]);
    }
}
