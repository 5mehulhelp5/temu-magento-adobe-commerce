<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Listing;

class SaveCategoryTemplate extends \M2E\Temu\Controller\Adminhtml\AbstractListing
{
    private \M2E\Temu\Model\Product\AssignCategoryTemplateService $assignCategoryTemplateService;

    public function __construct(
        \M2E\Temu\Model\Product\AssignCategoryTemplateService $assignCategoryTemplateService,
        $context = null
    ) {
        parent::__construct($context);
        $this->assignCategoryTemplateService = $assignCategoryTemplateService;
    }

    public function execute()
    {
        $productIds = $this->getRequest()->getParam('products_ids');
        if (empty($productIds)) {
            return $this->getResult();
        }

        $templateCategoryId = $this->getRequest()->getParam('template_category_id');

        if (empty($templateCategoryId)) {
            throw new \M2E\Temu\Model\Exception\Logic('Choose category');
        }

        $this->assignCategoryTemplateService->assignToProductIds(
            explode(',', $productIds),
            (int)$templateCategoryId
        );

        return $this->getResult();
    }
}
