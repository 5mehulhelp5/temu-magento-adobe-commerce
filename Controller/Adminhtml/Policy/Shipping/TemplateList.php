<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Policy\Shipping;

class TemplateList extends \M2E\Temu\Controller\Adminhtml\AbstractTemplate
{
    private \M2E\Temu\Model\Policy\Shipping\ShippingService $shippingService;
    private \M2E\Temu\Model\Account\Repository $accountRepository;

    public function __construct(
        \M2E\Temu\Model\Policy\Shipping\ShippingService $shippingService,
        \M2E\Temu\Model\Account\Repository $accountRepository,
        \M2E\Temu\Model\Policy\Manager $templateManager
    ) {
        parent::__construct($templateManager);
        $this->accountRepository = $accountRepository;
        $this->shippingService = $shippingService;
    }

    public function execute()
    {
        $accountId = (int)$this->getRequest()->getParam('account_id');
        $account = $this->accountRepository->find($accountId);

        if ($account === null) {
            $this->setJsonContent([
                'result' => false,
                'message' => 'Account Id is required',
            ]);

            return $this->getResult();
        }

        $force = (bool)(int)$this->getRequest()->getParam('force', 0);

        $shippingTemplates = [];
        foreach ($this->shippingService->getAllTemplates($account, $force)->getAll() as $template) {
            $shippingTemplates[] = [
                'id' => $template->id,
                'title' => $template->name,
            ];
        }

        $this->setJsonContent([
            'result' => true,
            'templates' => $shippingTemplates,
        ]);

        return $this->getResult();
    }
}
