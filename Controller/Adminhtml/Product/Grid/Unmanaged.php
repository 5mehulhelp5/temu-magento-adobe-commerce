<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Product\Grid;

class Unmanaged extends \M2E\Temu\Controller\Adminhtml\AbstractListing
{
    use \M2E\Temu\Controller\Adminhtml\Listing\Wizard\WizardTrait;

    private \M2E\Temu\Model\Listing\Wizard\Repository $wizardRepository;
    private \M2E\Temu\Model\Account\Ui\RuntimeStorage $uiAccountRuntimeStorage;
    private \M2E\Temu\Model\Account\Repository $accountRepository;

    public function __construct(
        \M2E\Temu\Model\Listing\Wizard\Repository $wizardRepository,
        \M2E\Temu\Model\Account\Ui\RuntimeStorage $uiAccountRuntimeStorage,
        \M2E\Temu\Model\Account\Repository $accountRepository
    ) {
        parent::__construct();

        $this->uiAccountRuntimeStorage = $uiAccountRuntimeStorage;
        $this->accountRepository = $accountRepository;
        $this->wizardRepository = $wizardRepository;
    }

    public function execute()
    {
        $wizard = $this->wizardRepository->findNotCompletedWizardByType(\M2E\Temu\Model\Listing\Wizard::TYPE_UNMANAGED);

        if (null !== $wizard) {
            $this->getMessageManager()->addNoticeMessage(
                \__(
                    'Please make sure you finish adding new Products before moving to the next step.',
                ),
            );

            return $this->redirectToIndex($wizard->getId());
        }

        try {
            $this->loadAccount();
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $this->_redirect('*/listing/index');
        }

        $this->getResultPage()
             ->getConfig()
             ->getTitle()
             ->prepend(\__('All Unmanaged Items'));

        return $this->getResult();
    }

    private function loadAccount(): void
    {
        $accountId = $this->getRequest()->getParam('account');
        if (empty($accountId)) {
            $account = $this->accountRepository->getFirst();
        } else {
            $account = $this->accountRepository->get((int)$accountId);
        }

        $this->uiAccountRuntimeStorage->setAccount($account);
    }
}
