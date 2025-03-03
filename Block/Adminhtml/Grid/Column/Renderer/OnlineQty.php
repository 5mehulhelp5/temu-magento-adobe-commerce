<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Grid\Column\Renderer;

class OnlineQty extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected string $dataKeyQtySource = 'online_qty';
    protected string $dataKeyStatus = 'status';

    public function render(\Magento\Framework\DataObject $row)
    {
        $productStatus = $this->getStatus($row);

        if ($productStatus === \M2E\Temu\Model\Product::STATUS_NOT_LISTED) {
            return sprintf(
                '<span style="color: gray">%s</span>',
                __('Not Listed')
            );
        }

        $qty = $this->getQty($row);
        if (
            $productStatus === \M2E\Temu\Model\Product::STATUS_INACTIVE
            && $qty > 0
        ) {
            return sprintf(
                '<span style="color: gray; text-decoration: line-through;">%s</span>',
                $qty
            );
        }

        return $qty;
    }

    public function renderExport(\Magento\Framework\DataObject $row)
    {
        $qty = $this->getQty($row);

        return (string)$qty;
    }

    private function getQty(\Magento\Framework\DataObject $row): int
    {
        return (int)($row->getData($this->dataKeyQtySource) ?? 0);
    }

    private function getStatus(\Magento\Framework\DataObject $row): int
    {
        return (int)($row->getData($this->dataKeyStatus) ?? 0);
    }
}
