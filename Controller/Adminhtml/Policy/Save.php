<?php

namespace M2E\Temu\Controller\Adminhtml\Policy;

class Save extends \M2E\Temu\Controller\Adminhtml\AbstractTemplate
{
    private \M2E\Temu\Helper\Module\Wizard $wizardHelper;
    private \M2E\Core\Helper\Url $urlHelper;
    private \M2E\Temu\Model\Policy\Synchronization\SaveService $synchronizationSaveService;
    private \M2E\Temu\Model\Policy\SellingFormat\SaveService $sellingFormatSaveService;

    public function __construct(
        \M2E\Temu\Model\Policy\SellingFormat\SaveService $sellingFormatSaveService,
        \M2E\Temu\Model\Policy\Synchronization\SaveService $synchronizationSaveService,
        \M2E\Temu\Helper\Module\Wizard $wizardHelper,
        \M2E\Core\Helper\Url $urlHelper,
        \M2E\Temu\Model\Policy\Manager $templateManager
    ) {
        parent::__construct($templateManager);

        $this->wizardHelper = $wizardHelper;
        $this->urlHelper = $urlHelper;
        $this->synchronizationSaveService = $synchronizationSaveService;
        $this->sellingFormatSaveService = $sellingFormatSaveService;
    }

    public function execute()
    {
        $templates = [];
        $templateNicks = $this->templateManager->getAllTemplates();

        // ---------------------------------------
        foreach ($templateNicks as $nick) {
            if ($this->isSaveAllowed($nick)) {
                $template = $this->saveTemplate($nick);

                if ($template) {
                    $templates[] = [
                        'nick' => $nick,
                        'id' => (int)$template->getId(),
                        'title' => \M2E\Core\Helper\Data::escapeJs(
                            \M2E\Core\Helper\Data::escapeHtml($template->getTitle())
                        ),
                    ];
                }
            }
        }
        // ---------------------------------------

        // ---------------------------------------
        if ($this->isAjax()) {
            $this->setJsonContent($templates);

            return $this->getResult();
        }
        // ---------------------------------------

        if (count($templates) == 0) {
            $this->messageManager->addError(__('Policy was not saved.'));

            return $this->_redirect('*/*/index');
        }

        $template = array_shift($templates);

        $this->messageManager->addSuccess(__('Policy was saved.'));

        $extendedRoutersParams = [
            'edit' => [
                'id' => $template['id'],
                'nick' => $template['nick'],
                'close_on_save' => $this->getRequest()->getParam('close_on_save'),
            ],
        ];

        if ($this->wizardHelper->isActive(\M2E\Temu\Helper\View\Temu::WIZARD_INSTALLATION_NICK)) {
            $extendedRoutersParams['edit']['wizard'] = true;
        }

        return $this->_redirect(
            $this->urlHelper->getBackUrl(
                'list',
                [],
                $extendedRoutersParams
            )
        );
    }

    protected function isSaveAllowed($templateNick)
    {
        if (!$this->getRequest()->isPost()) {
            return false;
        }

        $requestedTemplateNick = $this->getRequest()->getPost('nick');

        if ($requestedTemplateNick === null) {
            return true;
        }

        if ($requestedTemplateNick == $templateNick) {
            return true;
        }

        return false;
    }

    protected function saveTemplate($nick)
    {
        $data = $this->getRequest()->getPost($nick);

        if ($data === null) {
            return null;
        }

        if ($nick === \M2E\Temu\Model\Policy\Manager::TEMPLATE_SYNCHRONIZATION) {
            return $this->synchronizationSaveService->save($data);
        }

        if ($nick === \M2E\Temu\Model\Policy\Manager::TEMPLATE_SELLING_FORMAT) {
            return $this->sellingFormatSaveService->save($data);
        }

        throw new \M2E\Temu\Model\Exception\Logic('Unknown nick ' . $nick);
    }
}
