<?php

namespace M2E\Temu\Controller\Adminhtml\Policy;

use M2E\Temu\Controller\Adminhtml\AbstractTemplate;

class Edit extends AbstractTemplate
{
    private \M2E\Temu\Helper\Component\Temu\Template\Switcher\DataLoader $dataLoader;
    private \M2E\Temu\Model\Policy\Synchronization\Repository $synchronizationRepository;
    private \M2E\Temu\Model\Policy\SynchronizationFactory $synchronizationFactory;
    private \M2E\Temu\Model\Policy\SellingFormatFactory $sellingFormatFactory;
    private \M2E\Temu\Model\Policy\SellingFormat\Repository $sellingFormatRepository;

    public function __construct(
        \M2E\Temu\Model\Policy\SellingFormatFactory $sellingFormatFactory,
        \M2E\Temu\Model\Policy\SellingFormat\Repository $sellingFormatRepository,
        \M2E\Temu\Model\Policy\Synchronization\Repository $synchronizationRepository,
        \M2E\Temu\Model\Policy\SynchronizationFactory $synchronizationFactory,
        \M2E\Temu\Helper\Component\Temu\Template\Switcher\DataLoader $dataLoader,
        \M2E\Temu\Model\Policy\Manager $templateManager
    ) {
        parent::__construct($templateManager);

        $this->dataLoader = $dataLoader;
        $this->synchronizationRepository = $synchronizationRepository;
        $this->synchronizationFactory = $synchronizationFactory;
        $this->sellingFormatFactory = $sellingFormatFactory;
        $this->sellingFormatRepository = $sellingFormatRepository;
    }

    public function execute()
    {
        // ---------------------------------------
        $id = $this->getRequest()->getParam('id');
        $nick = $this->getRequest()->getParam('nick');
        // ---------------------------------------

        if ($nick === \M2E\Temu\Model\Policy\Manager::TEMPLATE_SYNCHRONIZATION) {
            return $this->executeSynchronizationTemplate($id);
        }

        if ($nick === \M2E\Temu\Model\Policy\Manager::TEMPLATE_SELLING_FORMAT) {
            return $this->executeSellingFormatTemplate($id);
        }

        throw new \M2E\Temu\Model\Exception\Logic('Unknown nick ' . $nick);
    }

    private function executeSynchronizationTemplate($id)
    {
        $template = $this->synchronizationRepository->find((int)$id);
        if ($template === null) {
            $template = $this->synchronizationFactory->create();
        }

        if (!$template->getId() && $id) {
            $this->getMessageManager()->addError(__('Policy does not exist.'));

            return $this->_redirect('*/*/index');
        }

        $dataLoader = $this->dataLoader;
        $dataLoader->load($template);

        // ---------------------------------------

        $this->setPageHelpLink('https://docs-m2.m2epro.com/docs/m2e-temu-policies/');

        if ($template->getId()) {
            $headerText =
                __(
                    'Edit "%template_title" Synchronization Policy',
                    [
                        'template_title' => \M2E\Core\Helper\Data::escapeHtml($template->getTitle()),
                    ],
                );
        } else {
            $headerText = __('Add Synchronization Policy');
        }

        $content = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Template\Edit::class,
            '',
            [
                'data' => [
                    'template_nick' => \M2E\Temu\Model\Policy\Manager::TEMPLATE_SYNCHRONIZATION,
                ],
            ]
        );

        $content->toHtml();

        $this->getResult()->getConfig()->getTitle()->prepend($headerText);
        $this->addContent($content);

        return $this->getResult();
    }

    private function executeSellingFormatTemplate($id)
    {
        $template = $this->sellingFormatRepository->find((int)$id);
        if ($template === null) {
            $template = $this->sellingFormatFactory->create();
        }

        if (!$template->getId() && $id) {
            $this->getMessageManager()->addError(__('Policy does not exist.'));

            return $this->_redirect('*/*/index');
        }

        $dataLoader = $this->dataLoader;
        $dataLoader->load($template);

        // ---------------------------------------

        $this->setPageHelpLink('https://docs-m2.m2epro.com/docs/m2e-temu-policies/');

        if ($template->getId()) {
            $headerText =
                __(
                    'Edit "%template_title" Selling Policy',
                    [
                        'template_title' => \M2E\Core\Helper\Data::escapeHtml($template->getTitle()),
                    ],
                );
        } else {
            $headerText = __('Add Selling Policy');
        }

        $content = $this->getLayout()->createBlock(
            \M2E\Temu\Block\Adminhtml\Template\Edit::class,
            '',
            [
                'data' => [
                    'template_nick' => \M2E\Temu\Model\Policy\Manager::TEMPLATE_SELLING_FORMAT,
                ],
            ]
        );

        $content->toHtml();

        $this->getResult()->getConfig()->getTitle()->prepend($headerText);
        $this->addContent($content);

        return $this->getResult();
    }
}
