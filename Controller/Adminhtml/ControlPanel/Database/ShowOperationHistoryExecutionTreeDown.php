<?php

namespace M2E\Temu\Controller\Adminhtml\ControlPanel\Database;

class ShowOperationHistoryExecutionTreeDown extends AbstractTable
{
    private \M2E\Temu\Model\OperationHistoryFactory $operationHistoryFactory;
    private \M2E\Temu\Model\OperationHistory\Repository $repository;

    public function __construct(
        \M2E\Temu\Model\OperationHistoryFactory $operationHistoryFactory,
        \M2E\Temu\Model\OperationHistory\Repository $repository,
        \M2E\Core\Model\ControlPanel\Database\TableModelFactory $databaseTableFactory,
        \M2E\Temu\Helper\Data\Cache\Permanent $cache
    ) {
        parent::__construct($databaseTableFactory, $cache);
        $this->operationHistoryFactory = $operationHistoryFactory;
        $this->repository = $repository;
    }

    public function execute()
    {
        $operationHistoryId = $this->getRequest()->getParam('operation_history_id');
        if (empty($operationHistoryId)) {
            $this->getMessageManager()->addErrorMessage('Operation history ID is not presented.');

            return $this->redirectToTablePage(
                \M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_OPERATION_HISTORY,
            );
        }

        $history = $this->repository->get((int)$operationHistoryId);
        $operationHistory = $this->operationHistoryFactory->create()
                                                          ->setObject($history);

        while ($parentId = $operationHistory->getObject()->getData('parent_id')) {
            $object = $operationHistory->load($parentId);
            $operationHistory->setObject($object);
        }

        $this->getResponse()->setBody(
            '<pre>' . $operationHistory->getExecutionTreeDownInfo() . '</pre>',
        );
    }
}
