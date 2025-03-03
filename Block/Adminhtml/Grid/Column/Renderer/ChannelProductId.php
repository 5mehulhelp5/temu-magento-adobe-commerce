<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Grid\Column\Renderer;

class ChannelProductId extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected string $dataKeyStatus = 'status';
    protected string $dataKeyProductId = 'product_id';

    public function render(\Magento\Framework\DataObject $row): string
    {
        $productStatus = $this->getStatus($row);
        if ($productStatus === \M2E\Temu\Model\Product::STATUS_NOT_LISTED) {
            return sprintf('<span style="color: gray;">%s</span>', __('Not Listed'));
        }

        $channelProductId = $this->getChannelProductId($row);

        if (empty($channelProductId)) {
            return (string)__('N/A');
        }

        return sprintf('<span >%s</span>', $channelProductId);
    }

    public function renderExport(\Magento\Framework\DataObject $row): string
    {
        return $this->getChannelProductId($row);
    }

    private function getChannelProductId(\Magento\Framework\DataObject $row): string
    {
        return (string)($row->getData($this->dataKeyProductId) ?? '');
    }

    private function getStatus(\Magento\Framework\DataObject $row): int
    {
        return (int)($row->getData($this->dataKeyStatus) ?? 0);
    }
}
