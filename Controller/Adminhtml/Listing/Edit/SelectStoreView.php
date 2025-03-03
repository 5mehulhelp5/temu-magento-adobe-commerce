<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Listing\Edit;

class SelectStoreView extends \M2E\Temu\Controller\Adminhtml\AbstractMain
{
    private \M2E\Temu\Model\Listing\Repository $listingRepository;

    public function __construct(
        \M2E\Temu\Model\Listing\Repository $listingRepository,
        \M2E\Temu\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->listingRepository = $listingRepository;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (empty($params['id'])) {
            return $this->getResponse()->setBody(__('You should provide correct parameters.'));
        }

        $listing = $this->listingRepository->get((int) $params['id']);

        $this->setAjaxContent(
            $this->getLayout()->createBlock(
                \M2E\Temu\Block\Adminhtml\Listing\Edit\EditStoreView::class,
                '',
                ['listing' => $listing]
            )
        );

        return $this->getResult();
    }
}
