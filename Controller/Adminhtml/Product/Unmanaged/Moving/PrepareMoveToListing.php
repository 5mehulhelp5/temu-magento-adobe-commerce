<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Product\Unmanaged\Moving;

class PrepareMoveToListing extends \M2E\Temu\Controller\Adminhtml\AbstractListing
{
    private \M2E\Temu\Helper\Data\Session $sessionHelper;
    private \M2E\Temu\Model\UnmanagedProduct\Repository $unmanagedRepository;

    public function __construct(
        \M2E\Temu\Model\UnmanagedProduct\Repository $unmanagedRepository,
        \M2E\Temu\Helper\Data\Session $sessionHelper,
        \M2E\Temu\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->sessionHelper = $sessionHelper;
        $this->unmanagedRepository = $unmanagedRepository;
    }

    public function execute() //TODO consider removal or adding proper functionality
    {
        $accountId = (int)$this->getRequest()->getParam('account_id');
        $selectedProductsIds = (array)$this->getRequest()->getParam('unmanaged_product_ids');

        $sessionKey = \M2E\Temu\Helper\View::MOVING_LISTING_OTHER_SELECTED_SESSION_KEY;
        $this->sessionHelper->setValue($sessionKey, $selectedProductsIds);

        $response = [
            'result' => true,
        ];

        $this->setJsonContent($response);

        return $this->getResult();
    }
}
