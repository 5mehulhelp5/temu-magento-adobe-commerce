<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Listing\Wizard\Review;

use M2E\Temu\Model\Listing\Wizard\StepDeclarationCollectionFactory;

class View extends \M2E\Temu\Controller\Adminhtml\Listing\Wizard\StepAbstract
{
    use \M2E\Temu\Controller\Adminhtml\Listing\Wizard\WizardTrait;

    protected function getStepNick(): string
    {
        return StepDeclarationCollectionFactory::STEP_REVIEW;
    }

    protected function process(\M2E\Temu\Model\Listing $listing)
    {
        if ($this->getRequest()->getParam('type', '') === \M2E\Temu\Model\Listing\Wizard::TYPE_UNMANAGED) {
            /** @var \M2E\Temu\Block\Adminhtml\Listing\Wizard\ReviewUnmanaged $blockReview */
            $blockReview = $this->getLayout()->createBlock(
                \M2E\Temu\Block\Adminhtml\Listing\Wizard\ReviewUnmanaged::class,
            );
        } else {
            /** @var \M2E\Temu\Block\Adminhtml\Listing\Wizard\Review $blockReview */
            $blockReview = $this->getLayout()->createBlock(
                \M2E\Temu\Block\Adminhtml\Listing\Wizard\Review::class,
            );
        }

        $this->getResultPage()
             ->getConfig()
             ->getTitle()
             ->prepend(__('Congratulations'));

        $this->addContent($blockReview);

        return $this->getResult();
    }
}
