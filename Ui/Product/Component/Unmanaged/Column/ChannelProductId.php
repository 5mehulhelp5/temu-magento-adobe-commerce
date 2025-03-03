<?php

declare(strict_types=1);

namespace M2E\Temu\Ui\Product\Component\Unmanaged\Column;

class ChannelProductId extends \Magento\Ui\Component\Listing\Columns\Column
{
    private \M2E\Temu\Model\Product\Ui\RuntimeStorage $productUiRuntimeStorage;

    public function __construct(
        \M2E\Temu\Model\Product\Ui\RuntimeStorage $productUiRuntimeStorage,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->productUiRuntimeStorage = $productUiRuntimeStorage;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$row) {
            $channelProductId = $row['channel_product_id'];

            $row['channel_product_id'] = $channelProductId;
        }

        return $dataSource;
    }
}
