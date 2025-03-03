<?php

namespace M2E\Temu\Controller\Adminhtml\Policy;

use M2E\Temu\Controller\Adminhtml\AbstractTemplate;

class Delete extends AbstractTemplate
{
    private \M2E\Temu\Model\Policy\SellingFormat\Repository $sellingFormatRepository;
    private \M2E\Temu\Model\Policy\Synchronization\Repository $synchronizationRepository;

    public function __construct(
        \M2E\Temu\Model\Policy\SellingFormat\Repository $sellingFormatRepository,
        \M2E\Temu\Model\Policy\Synchronization\Repository $synchronizationRepository,
        \M2E\Temu\Model\Policy\Manager $templateManager
    ) {
        parent::__construct($templateManager);
        $this->sellingFormatRepository = $sellingFormatRepository;
        $this->synchronizationRepository = $synchronizationRepository;
    }

    public function execute()
    {
        // ---------------------------------------
        $id = $this->getRequest()->getParam('id');
        $nick = $this->getRequest()->getParam('nick');
        // ---------------------------------------

        if ($nick === \M2E\Temu\Model\Policy\Manager::TEMPLATE_SYNCHRONIZATION) {
            return $this->deleteSynchronizationTemplate($id);
        }

        if ($nick === \M2E\Temu\Model\Policy\Manager::TEMPLATE_SELLING_FORMAT) {
            return $this->deleteSellingFormatTemplate($id);
        }

        throw new \M2E\Temu\Model\Exception\Logic('Unknown nick ' . $nick);
    }

    private function deleteSynchronizationTemplate($id): \Magento\Framework\App\ResponseInterface
    {
        try {
            $template = $this->synchronizationRepository->get((int)$id);
        } catch (\M2E\Temu\Model\Exception\Logic $exception) {
            $this->messageManager
                ->addError(__($exception->getMessage()));
            return $this->_redirect('*/*/index');
        }

        if ($template->isLocked()) {
            $this->messageManager
                ->addError(__('Policy cannot be deleted as it is used in Listing Settings.'));

            return $this->_redirect('*/*/index');
        }

        $this->synchronizationRepository->delete($template);

        $this->messageManager
                ->addSuccess(__('Policy was deleted.'));

        return $this->_redirect('*/*/index');
    }

    private function deleteSellingFormatTemplate($id)
    {
        try {
            $template = $this->sellingFormatRepository->get((int)$id);
        } catch (\M2E\Temu\Model\Exception\Logic $exception) {
            $this->messageManager
                ->addError(__($exception->getMessage()));
            return $this->_redirect('*/*/index');
        }

        if ($template->isLocked()) {
            $this->messageManager
                ->addError(__('Policy cannot be deleted as it is used in Listing Settings.'));

            return $this->_redirect('*/*/index');
        }

        $this->sellingFormatRepository->delete($template);

        $this->messageManager
            ->addSuccess(__('Policy was deleted.'));

        return $this->_redirect('*/*/index');
    }
}
