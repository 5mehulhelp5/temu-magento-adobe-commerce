<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Product\Unmanaged\Mapping;

class MapGrid extends \M2E\Temu\Controller\Adminhtml\AbstractListing
{
    private \M2E\Temu\Model\UnmanagedProduct\Repository $unmanagedRepository;

    public function __construct(
        \M2E\Temu\Model\UnmanagedProduct\Repository $unmanagedRepository,
        $context = null
    ) {
        parent::__construct($context);
        $this->unmanagedRepository = $unmanagedRepository;
    }

    public function execute()
    {
        $unmanagedId = (int)$this->getRequest()->getParam('unmanaged_product_id');
        $unmanagedProduct = $this->unmanagedRepository->findById($unmanagedId);

        $block = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Listing\Mapping\Grid::class,
            '',
            [
                'data' => [
                    'unmanaged_product_id' => $unmanagedId,
                    'grid_url' => \M2E\Temu\Model\UnmanagedProduct\Ui\UrlHelper::PATH_UNMANAGED_MAP_GRID,
                    'product_type' => $unmanagedProduct->isSimple()
                        ? \M2E\Temu\Helper\Magento\Product::TYPE_SIMPLE
                        : \M2E\Temu\Helper\Magento\Product::TYPE_CONFIGURABLE,
                ],
            ]
        );

        $this->setAjaxContent($block);

        return $this->getResult();
    }
}
