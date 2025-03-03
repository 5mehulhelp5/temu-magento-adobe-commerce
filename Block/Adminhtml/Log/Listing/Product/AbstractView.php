<?php

namespace M2E\Temu\Block\Adminhtml\Log\Listing\Product;

abstract class AbstractView extends \M2E\Temu\Block\Adminhtml\Log\Listing\AbstractView
{
    private ?\M2E\Temu\Model\Listing $listing = null;
    private ?\M2E\Temu\Model\Product $listingProduct = null;
    private \M2E\Temu\Helper\Data\Session $sessionDataHelper;
    private \M2E\Temu\Model\Listing\Repository $listingRepository;
    private \M2E\Temu\Model\Product\Repository $listingProductRepository;

    public function __construct(
        \M2E\Temu\Model\Product\Repository $listingProductRepository,
        \M2E\Temu\Model\Listing\Repository $listingRepository,
        \M2E\Temu\Helper\Data\Session $sessionDataHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Widget $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sessionDataHelper = $sessionDataHelper;
        $this->listingRepository = $listingRepository;
        $this->listingProductRepository = $listingProductRepository;
    }

    protected function getFiltersHtml()
    {
        $sessionViewMode = $this->sessionDataHelper->getValue('temu_log_listing_view_mode');

        $uniqueMessageFilterBlockHtml = '';
        if ($sessionViewMode === \M2E\Temu\Block\Adminhtml\Log\Listing\View\Switcher::VIEW_MODE_SEPARATED) {
            $uniqueMessageFilterBlockHtml = $this->uniqueMessageFilterBlock->toHtml();
        }

        if ($this->getListingId()) {
            $html = $this->getStaticFilterHtml(
                $this->accountSwitcherBlock->getLabel(),
                $this->getListing()->getAccount()->getTitle()
            );
            $html .= $uniqueMessageFilterBlockHtml;

            return $this->getSwitcherHtml($html);
        }

        if ($this->getListingProductId()) {
            $html = $this->getStaticFilterHtml(
                $this->accountSwitcherBlock->getLabel(),
                $this->getListingProduct()->getListing()->getAccount()->getTitle()
            );

            return $this->getSwitcherHtml($html);
        }

        $html = $this->accountSwitcherBlock->toHtml()
            . $uniqueMessageFilterBlockHtml;

        return $this->getSwitcherHtml($html);
    }

    private function getSwitcherHtml(string $html): string
    {
        return
            '<div class="switcher-separator"></div>'
            . $html;
    }

    public function getListingId()
    {
        return $this->getRequest()->getParam(
            \M2E\Temu\Block\Adminhtml\Log\AbstractGrid::LISTING_ID_FIELD,
            false
        );
    }

    public function getListing(): \M2E\Temu\Model\Listing
    {
        if ($this->listing === null) {
            $this->listing = $this->listingRepository->get($this->getListingId());
        }

        return $this->listing;
    }

    public function getListingProductId()
    {
        return $this->getRequest()->getParam(
            \M2E\Temu\Block\Adminhtml\Log\AbstractGrid::LISTING_PRODUCT_ID_FIELD,
            false
        );
    }

    /**
     * @return \M2E\Temu\Model\Product
     * @throws \M2E\Temu\Model\Exception\Logic
     */
    public function getListingProduct(): \M2E\Temu\Model\Product
    {
        if ($this->listingProduct === null) {
            $this->listingProduct = $this->listingProductRepository->get($this->getListingProductId());
        }

        return $this->listingProduct;
    }
}
