<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Magento\Grid\Column\Renderer;

class Opc extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Number
{
    use \M2E\Temu\Block\Adminhtml\Traits\BlockTrait;

    public function render(\Magento\Framework\DataObject $row)
    {
        $opc = $row->getData('opc');
        $url = $row->getData('online_product_url');

        return '<a href="' . $url . '" target="_blank">' . $opc . '</a>';
    }

    public function renderExport(\Magento\Framework\DataObject $row)
    {
        return $this->_getValue($row) ?? '';
    }
}
