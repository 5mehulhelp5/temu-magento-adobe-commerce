<?php

declare(strict_types=1);

namespace M2E\Temu\Controller\Adminhtml\ControlPanel\Database;

class ManageTable extends AbstractTable
{
    protected \M2E\Temu\Helper\View\ControlPanel $controlPanelHelper;

    public function __construct(
        \M2E\Temu\Helper\View\ControlPanel $controlPanelHelper,
        \M2E\Core\Model\ControlPanel\Database\TableModelFactory $databaseTableFactory,
        \M2E\Temu\Helper\Data\Cache\Permanent $cache
    ) {
        parent::__construct($databaseTableFactory, $cache);
        $this->controlPanelHelper = $controlPanelHelper;
    }

    public function execute()
    {
        $table = $this->getRequest()->getParam('table');

        if ($table === null) {
            return $this->_redirect($this->controlPanelHelper->getPageDatabaseTabUrl());
        }

        $this->addContent(
            $this->getLayout()->createBlock(
                \M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database\Table::class,
                '',
                ['tableName' => $table],
            ),
        );

        return $this->getResultPage();
    }
}
