<?php

declare(strict_types=1);

namespace M2E\Temu\Block\Adminhtml\Listing\Wizard;

class ReviewUnmanaged extends \M2E\Temu\Block\Adminhtml\Magento\AbstractContainer
{
    use ReviewTrait;

    private \M2E\Temu\Model\Listing\Wizard\Ui\RuntimeStorage $uiWizardRuntimeStorage;
    private \M2E\Temu\Model\UnmanagedProduct\Ui\UrlHelper $urlHelper;

    public function __construct(
        \M2E\Temu\Model\Listing\Wizard\Ui\RuntimeStorage $uiWizardRuntimeStorage,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Widget $context,
        \M2E\Temu\Model\UnmanagedProduct\Ui\UrlHelper $urlHelper,
        array $data = []
    ) {
        $this->uiWizardRuntimeStorage = $uiWizardRuntimeStorage;
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    public function _construct(): void
    {
        parent::_construct();

        $this->setId('listingProductReview');
        $this->setTemplate('listing/wizard/review_unmanaged.phtml');
    }

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        $this->addGoToListingButton();
        $this->addGoToUnmanagedButton();
    }

    private function addGoToUnmanagedButton(): void
    {
        $accountId = $this->uiWizardRuntimeStorage->getManager()->getListing()->getAccountId();
        $buttonBlock = $this->getLayout()
                            ->createBlock(\M2E\Temu\Block\Adminhtml\Magento\Button::class)
                            ->setData(
                                [
                                    'label' => __('Back to Unmanaged Items'),
                                    'onclick' => 'setLocation(\'' . $this->generateCompleteUrl(
                                        false,
                                        $this->urlHelper->getGridUrl(
                                            ['account' => $accountId]
                                        )
                                    ) . '\');',
                                    'class' => 'primary go',
                                ],
                            );

        $this->setChild('go_to_unmanaged', $buttonBlock);
    }
}
