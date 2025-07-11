<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Listing\Wizard\Product;

class GetProductsIds extends \M2E\Temu\Controller\Adminhtml\AbstractListing
{
    use \M2E\Temu\Controller\Adminhtml\Listing\Wizard\WizardTrait;

    private \M2E\Temu\Model\Listing\Wizard\ManagerFactory $wizardManagerFactory;

    public function __construct(
        \M2E\Temu\Model\Listing\Wizard\ManagerFactory $wizardManagerFactory
    ) {
        parent::__construct();

        $this->wizardManagerFactory = $wizardManagerFactory;
    }

    public function execute()
    {
        $id = $this->getWizardIdFromRequest();
        $manager = $this->wizardManagerFactory->createById($id);

        $stepData = $manager->getStepData(\M2E\Temu\Model\Listing\Wizard\StepDeclarationCollectionFactory::STEP_SELECT_PRODUCTS);
        $selectedProductsIds = $stepData['products_ids'] ?? [];

        $this->setJsonContent([
            'ids' => $selectedProductsIds,
        ]);

        return $this->getResult();
    }
}
