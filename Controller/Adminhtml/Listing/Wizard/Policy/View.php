<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Listing\Wizard\Policy;

use M2E\Temu\Model\Listing\Wizard\StepDeclarationCollectionFactory;

class View extends \M2E\Temu\Controller\Adminhtml\Listing\Wizard\StepAbstract
{
    use \M2E\Temu\Controller\Adminhtml\Listing\Wizard\WizardTrait;

    public function __construct(
        \M2E\Temu\Model\Listing\Ui\RuntimeStorage $uiListingRuntimeStorage,
        \M2E\Temu\Model\Listing\Wizard\ManagerFactory $wizardManagerFactory,
        \M2E\Temu\Model\Listing\Wizard\Ui\RuntimeStorage $uiWizardRuntimeStorage
    ) {
        parent::__construct($wizardManagerFactory, $uiListingRuntimeStorage, $uiWizardRuntimeStorage);
    }

    protected function getStepNick(): string
    {
        return StepDeclarationCollectionFactory::STEP_POLICY_SETTINGS;
    }

    protected function process(\M2E\Temu\Model\Listing $listing)
    {
        if ($this->isNeedSkipStep()) {
            $this->getWizardManager()
                 ->completeStep(StepDeclarationCollectionFactory::STEP_POLICY_SETTINGS, true);

            return $this->redirectToIndex($this->getWizardManager()->getWizardId());
        }

        $this->getResultPage()
             ->getConfig()
             ->getTitle()->prepend(__('Add Policies'));

        $this->addContent(
            $this->getLayout()->createBlock(
                \M2E\Temu\Block\Adminhtml\Listing\Wizard\Policy\View::class,
                '',
                [
                    'listing' => $listing,
                    'id' => $listing->getId()
                ],
            ),
        );

        return $this->getResult();
    }

    private function isNeedSkipStep(): bool
    {
        if ($this->getWizardManager()->getListing()->isAllRequiredPoliciesExist()) {
            return true;
        }

        return false;
    }
}
