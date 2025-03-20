<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Listing\Wizard\Settings;

use M2E\Temu\Model\Listing\Wizard\StepDeclarationCollectionFactory;
use M2E\Temu\Model\Settings;

class View extends \M2E\Temu\Controller\Adminhtml\Listing\Wizard\StepAbstract
{
    use \M2E\Temu\Controller\Adminhtml\Listing\Wizard\WizardTrait;

    private \M2E\Temu\Model\Settings $settings;
    private \M2E\Temu\Model\Product\PackageDimensionFinder $packageDimensionFinder;

    public function __construct(
        \M2E\Temu\Model\Settings $settings,
        \M2E\Temu\Model\Listing\Ui\RuntimeStorage $uiListingRuntimeStorage,
        \M2E\Temu\Model\Listing\Wizard\ManagerFactory $wizardManagerFactory,
        \M2E\Temu\Model\Listing\Wizard\Ui\RuntimeStorage $uiWizardRuntimeStorage,
        \M2E\Temu\Model\Product\PackageDimensionFinder $packageDimensionFinder
    ) {
        parent::__construct($wizardManagerFactory, $uiListingRuntimeStorage, $uiWizardRuntimeStorage);
        $this->settings = $settings;
        $this->packageDimensionFinder = $packageDimensionFinder;
    }

    protected function getStepNick(): string
    {
        return StepDeclarationCollectionFactory::STEP_SETTINGS;
    }

    protected function process(\M2E\Temu\Model\Listing $listing)
    {
        if ($this->isNeedSkipStep()) {
            $this->getWizardManager()
                 ->completeStep(StepDeclarationCollectionFactory::STEP_SETTINGS, true);

            return $this->redirectToIndex($this->getWizardManager()->getWizardId());
        }

        $this->getResultPage()
             ->getConfig()
             ->getTitle()->prepend(__('Add Settings'));

        $this->addContent(
            $this->getLayout()->createBlock(
                \M2E\Temu\Block\Adminhtml\Listing\Wizard\Settings\View::class,
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
        if (
            $this->settings->isPackageDimensionModeNotSet(Settings::DIMENSION_TYPE_WIDTH)
            || $this->settings->isPackageDimensionModeNotSet(Settings::DIMENSION_TYPE_LENGTH)
            || $this->settings->isPackageDimensionModeNotSet(Settings::DIMENSION_TYPE_HEIGHT)
            || $this->settings->isPackageDimensionModeNotSet(Settings::DIMENSION_TYPE_WEIGHT)
        ) {
            return false;
        }

        return true;
    }
}
