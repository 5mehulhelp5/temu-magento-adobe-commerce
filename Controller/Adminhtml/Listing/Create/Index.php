<?php

namespace M2E\Temu\Controller\Adminhtml\Listing\Create;

use M2E\Temu\Model\Listing;

class Index extends \M2E\Temu\Controller\Adminhtml\AbstractListing
{
    private Listing\Transferring $transferring;
    private \M2E\Temu\Model\Listing\LogService $listingLogService;
    private \M2E\Temu\Helper\Data\Session $sessionHelper;
    private \M2E\Temu\Model\ListingFactory $listingFactory;
    private Listing\Repository $listingRepository;
    private \M2E\Temu\Helper\Module\Wizard $wizardHelper;
    private \M2E\Temu\Model\Listing\Wizard\Create $createModel;

    public function __construct(
        \M2E\Temu\Model\Listing\Repository $listingRepository,
        \M2E\Temu\Model\ListingFactory $listingFactory,
        \M2E\Temu\Helper\Data\Session $sessionHelper,
        \M2E\Temu\Model\Listing\LogService $listingLogService,
        Listing\Transferring $transferring,
        \M2E\Temu\Helper\Module\Wizard $wizardHelper,
        \M2E\Temu\Model\Listing\Wizard\Create $createModel
    ) {
        parent::__construct();
        $this->transferring = $transferring;
        $this->listingLogService = $listingLogService;
        $this->sessionHelper = $sessionHelper;
        $this->listingFactory = $listingFactory;
        $this->listingRepository = $listingRepository;
        $this->wizardHelper = $wizardHelper;
        $this->createModel = $createModel;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('M2E_Temu::listings_items');
    }

    public function execute()
    {
        $step = (int)$this->getRequest()->getParam('step');

        switch ($step) {
            case 1:
                $this->stepOne();
                break;
            case 2:
                $this->stepTwo();
                if ($this->getRequest()->isPost() && $this->isCreationModeListingOnly()) {
                    // closing window for Unmanaged products moving in new listing creation

                    return $this->getRawResult();
                }
                break;
            default:
                $this->clearSession();
                $this->_redirect('*/*/index', ['_current' => true, 'step' => 1]);
                break;
        }

        $this->getResultPage()->getConfig()->getTitle()->prepend(\__('New Listing Creation'));
        $this->setPageHelpLink('https://docs-m2.m2epro.com/docs/create-m2e-temu-listing/');

        return $this->getResult();
    }

    private function stepOne()
    {
        if ($this->getRequest()->getParam('clear')) {
            $this->clearSession();
            $this->getRequest()->setParam('clear', null);
            $this->_redirect('*/*/index', ['_current' => true, 'step' => 1]);

            return;
        }

        $this->setWizardStep('listingGeneral');

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            // clear session data if user came back to the first step and changed the marketplace
            // ---------------------------------------
            if (
                $this->getSessionValue('shop_id')
                && (int)$this->getSessionValue('shop_id') != (int)$post['shop_id']
            ) {
                $this->clearSession();
            }

            $this->setSessionValue('title', strip_tags($post['title']));
            $this->setSessionValue('account_id', (int)$post['account_id']);
            $this->setSessionValue('site_id', (int)$post['site_id']);
            $this->setSessionValue('store_id', (int)$post['store_id']);

            $this->_redirect('*/*/index', ['_current' => true, 'step' => 2]);

            return;
        }

        $listingOnlyMode = \M2E\Temu\Helper\View::LISTING_CREATION_MODE_LISTING_ONLY;
        if ($this->getRequest()->getParam('creation_mode') == $listingOnlyMode) {
            $this->setSessionValue('creation_mode', $listingOnlyMode);
        }

        $this->addContent(
            $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Listing\Create\General::class)
        );
    }

    private function stepTwo()
    {
        if ($this->getSessionValue('account_id') === null) {
            $this->clearSession();
            $this->_redirect('*/*/index', ['_current' => true, 'step' => 1]);

            return;
        }

        if ($this->getRequest()->isPost()) {
            $form = $this->getLayout()->createBlock(
                \M2E\Temu\Block\Adminhtml\Listing\Create\Templates\Form::class
            );
            $dataKeys = $form->getDefaultFieldsValues();

            $post = $this->getRequest()->getPost();
            foreach ($dataKeys as $key => $value) {
                $this->setSessionValue($key, $post[$key]);
            }

            $listing = $this->createListing();

            if ($listingId = $this->getRequest()->getParam('listing_id')) {
                $this->transferring->setListing(
                    $this->listingRepository->get($listingId)
                );

                $this->clearSession();
                $this->transferring->setTargetListingId($listing->getId());

                $this->_redirect(
                    '*/listing/transferring/index',
                    [
                        'listing_id' => $listingId,
                        'step' => 3,
                    ]
                );

                return;
            }

            if ($this->isCreationModeListingOnly()) {
                // closing window for Unmanaged products moving in new listing creation
                $this->getRawResult()->setContents("<script>window.close();</script>");

                return;
            }

            $this->clearSession();

            if ((bool)$this->getRequest()->getParam('wizard', false)) {
                $this->setWizardStep('sourceMode');

                $this->_redirect('*/wizard_installationTemu');

                return;
            }

            $this->_redirect(
                '*/listing/index',
                []
            );

            return;
        }

        $this->setWizardStep('listingTemplates');
        $this->addContent(
            $this->getLayout()->createBlock(\M2E\Temu\Block\Adminhtml\Listing\Create\Templates::class)
        );
    }

    //########################################

    private function createListing()
    {
        $data = $this->getSessionValue();
        $model = $this->listingFactory->createEmpty();
        $model->addData($data);
        $this->listingRepository->save($model);

        $this->listingLogService->addListing(
            $model,
            \M2E\Core\Helper\Data::INITIATOR_USER,
            \M2E\Temu\Model\Listing\Log::ACTION_ADD_LISTING,
            null,
            (string)\__('Listing was Added'),
            \M2E\Temu\Model\Log\AbstractModel::TYPE_INFO
        );

        return $model;
    }

    //########################################

    protected function setSessionValue($key, $value): self
    {
        $sessionData = $this->getSessionValue();
        $sessionData[$key] = $value;

        $this->sessionHelper->setValue(Listing::CREATE_LISTING_SESSION_DATA, $sessionData);

        return $this;
    }

    protected function getSessionValue($key = null)
    {
        $sessionData = $this->sessionHelper->getValue(Listing::CREATE_LISTING_SESSION_DATA);
        if ($sessionData === null) {
            $sessionData = [];
        }

        if ($key === null) {
            return $sessionData;
        }

        return $sessionData[$key] ?? null;
    }

    //########################################

    private function clearSession()
    {
        $this->sessionHelper->setValue(Listing::CREATE_LISTING_SESSION_DATA, null);
    }

    //########################################

    private function setWizardStep($step)
    {
        if (!$this->wizardHelper->isActive(\M2E\Temu\Helper\View\Temu::WIZARD_INSTALLATION_NICK)) {
            return;
        }

        $this->wizardHelper->setStep(\M2E\Temu\Helper\View\Temu::WIZARD_INSTALLATION_NICK, $step);
    }

    //########################################

    private function isCreationModeListingOnly()
    {
        return $this->getSessionValue(
            'creation_mode'
        ) === \M2E\Temu\Helper\View::LISTING_CREATION_MODE_LISTING_ONLY;
    }

    //########################################
}
