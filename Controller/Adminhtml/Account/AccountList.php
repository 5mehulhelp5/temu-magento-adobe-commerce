<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\Account;

class AccountList extends \M2E\Temu\Controller\Adminhtml\AbstractAccount
{
    private \M2E\Temu\Model\Account\Repository $accountRepository;

    public function __construct(\M2E\Temu\Model\Account\Repository $accountRepository)
    {
        parent::__construct();
        $this->accountRepository = $accountRepository;
    }

    public function execute()
    {
        $accounts = $this->accountRepository->getAll();
        $accounts = array_map(static function (\M2E\Temu\Model\Account $entity) {
            return [
                'id' => $entity->getId(),
                'title' => $entity->getTitle(),
            ];
        }, $accounts);

        $this->setJsonContent([
            'result' => true,
            'accounts' => $accounts,
        ]);

        return $this->getResult();
    }
}
