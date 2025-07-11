<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Listing\Variation\Product\Manage;

class Index extends \M2E\Temu\Controller\Adminhtml\AbstractListing
{
    private \M2E\Temu\Model\Product\Repository $listingProductRepository;

    public function __construct(
        \M2E\Temu\Model\Product\Repository $listingProductRepository,
        $context = null
    ) {
        parent::__construct($context);
        $this->listingProductRepository = $listingProductRepository;
    }

    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id');

        if (empty($productId)) {
            $this->setAjaxContent('You should provide correct parameters.', false);

            return $this->getResult();
        }

        try {
            $listingProduct = $this->listingProductRepository->get((int)$productId);
        } catch (\M2E\Temu\Model\Exception $exception) {
            $this->setAjaxContent($exception->getMessage());

            return $this->getResult();
        }

        $view = $this
            ->getLayout()
            ->createBlock(
                \M2E\Temu\Block\Adminhtml\Listing\Variation\Product\Manage\View::class,
                '',
                [
                    'listingProduct' => $listingProduct,
                ]
            );

        $this->setAjaxContent($view);

        return $this->getResult();
    }
}
