<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Listing;

use M2E\Temu\Controller\Adminhtml\AbstractListing;

class GetErrorsSummary extends AbstractListing
{
    private \M2E\Temu\Model\ResourceModel\Listing\Log $listingLogResource;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\Listing\Log $listingLogResource,
        $context = null
    ) {
        parent::__construct($context);
        $this->listingLogResource = $listingLogResource;
    }

    public function execute()
    {
        $blockParams = [
            'action_ids' => $this->getRequest()->getParam('action_ids'),
            'table_name' => $this->listingLogResource->getMainTable(),
            'type_log' => 'listing',
        ];
        $block = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Listing\Log\ErrorsSummary::class,
            '',
            ['data' => $blockParams]
        );
        $this->setAjaxContent($block);

        return $this->getResult();
    }
}
