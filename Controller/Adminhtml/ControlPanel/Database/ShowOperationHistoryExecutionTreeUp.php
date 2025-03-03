<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\ControlPanel\Database;

class ShowOperationHistoryExecutionTreeUp extends AbstractTable
{
    private \M2E\Temu\Model\OperationHistory\Repository $repository;

    public function __construct(
        \M2E\Temu\Model\OperationHistory\Repository $repository,
        \M2E\Core\Model\ControlPanel\Database\TableModelFactory $databaseTableFactory,
        \M2E\Temu\Helper\Data\Cache\Permanent $cache
    ) {
        parent::__construct($databaseTableFactory, $cache);
        $this->repository = $repository;
    }

    public function execute()
    {
        $operationHistoryId = $this->getRequest()->getParam('operation_history_id');
        if (empty($operationHistoryId)) {
            $this->getMessageManager()->addErrorMessage('Operation history ID is not presented.');

            $this->redirectToTablePage(
                \M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_OPERATION_HISTORY
            );

            return;
        }

        $operationHistory = $this->repository->get((int)$operationHistoryId);

        $this->getResponse()->setBody(
            '<pre>' . $operationHistory->getExecutionTreeUpInfo() . '</pre>',
        );
    }
}
