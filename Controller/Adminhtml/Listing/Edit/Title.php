<?php

namespace M2E\Temu\Controller\Adminhtml\Listing\Edit;

use M2E\Temu\Controller\Adminhtml\AbstractListing;

class Title extends AbstractListing
{
    /** @var \M2E\Temu\Helper\Data\GlobalData */
    private $globalData;
    private \M2E\Temu\Model\Listing\Repository $listingRepository;

    public function __construct(
        \M2E\Temu\Model\Listing\Repository $listingRepository,
        \M2E\Temu\Helper\Data\GlobalData $globalData,
        \M2E\Temu\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->globalData = $globalData;
        $this->listingRepository = $listingRepository;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (empty($params['id'])) {
            return $this->getResponse()->setBody('You should provide correct parameters.');
        }

        $listing = $this->listingRepository->get($params['id']);

        if ($this->getRequest()->isPost()) {
            $listing->addData($params);
            $this->listingRepository->save($listing);

            return $this->getResult();
        }

        $this->globalData->setValue('edit_listing', $listing);

        $this->setAjaxContent(
            $this->getLayout()->createBlock(
                \M2E\Temu\Block\Adminhtml\Listing\Edit\Title::class,
                '',
                [
                    'listing' => $listing,
                ],
            )
        );

        return $this->getResult();
    }
}
