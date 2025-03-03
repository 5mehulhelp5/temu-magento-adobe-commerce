<?php

namespace M2E\Temu\Controller\Adminhtml\ControlPanel\Database;

class UpdateTableCells extends AbstractTable
{
    public function execute()
    {
        $ids = $this->prepareIds();
        $cellsValues = $this->prepareCellsValuesArray();
        $modelInstance = $this->getTableModel();

        if (empty($ids) || empty($cellsValues)) {
            return;
        }

        $modelInstance->updateEntries($ids, $cellsValues);
        $this->afterTableAction($modelInstance->getTableName());
    }
}
