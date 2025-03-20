<?php

namespace M2E\Temu\Block\Adminhtml\Template;

use M2E\Temu\Block\Adminhtml\Magento\Grid\AbstractGrid;
use Magento\Framework\DB\Select;
use M2E\Temu\Model\ResourceModel\Account as AccountResource;

class Grid extends AbstractGrid
{
    private \M2E\Temu\Model\ResourceModel\Collection\WrapperFactory $wrapperCollectionFactory;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    private \M2E\Temu\Model\ResourceModel\Policy\SellingFormat\CollectionFactory $sellingCollectionFactory;
    private \M2E\Temu\Model\ResourceModel\Policy\Synchronization\CollectionFactory $syncCollectionFactory;
    private \M2E\Temu\Model\ResourceModel\Policy\Description\CollectionFactory $descriptionCollectionFactory;
    private \M2E\Temu\Model\ResourceModel\Policy\Shipping\CollectionFactory $shippingCollectionFactory;
    private \M2E\Temu\Model\ResourceModel\Account $accountResource;
    private \M2E\Temu\Model\ResourceModel\Account\Collection $accountCollection;
    private \M2E\Temu\Model\ResourceModel\Account\CollectionFactory $accountCollectionFactory;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\Policy\SellingFormat\CollectionFactory $sellingCollectionFactory,
        \M2E\Temu\Model\ResourceModel\Policy\Synchronization\CollectionFactory $syncCollectionFactory,
        \M2E\Temu\Model\ResourceModel\Policy\Description\CollectionFactory $descriptionCollectionFactory,
        \M2E\Temu\Model\ResourceModel\Policy\Shipping\CollectionFactory $shippingCollectionFactory,
        \M2E\Temu\Model\ResourceModel\Account $accountResource,
        \M2E\Temu\Model\ResourceModel\Account\Collection $accountCollection,
        \M2E\Temu\Model\ResourceModel\Account\CollectionFactory $accountCollectionFactory,
        \M2E\Temu\Model\ResourceModel\Collection\WrapperFactory $wrapperCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->wrapperCollectionFactory = $wrapperCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->sellingCollectionFactory = $sellingCollectionFactory;
        $this->syncCollectionFactory = $syncCollectionFactory;
        $this->descriptionCollectionFactory = $descriptionCollectionFactory;
        $this->shippingCollectionFactory = $shippingCollectionFactory;
        $this->accountResource = $accountResource;
        $this->accountCollection = $accountCollection;
        $this->accountCollectionFactory = $accountCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {
        parent::_construct();

        $this->css->addFile('policy/grid.css');

        // Initialization block
        // ---------------------------------------
        $this->setId('temuTemplateGrid');
        // ---------------------------------------

        // Set default values
        // ---------------------------------------
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        // ---------------------------------------
    }

    protected function _prepareCollection()
    {
        // Prepare selling format collection
        // ---------------------------------------
        $collectionSellingFormat = $this->sellingCollectionFactory->create();
        $collectionSellingFormat->getSelect()->reset(Select::COLUMNS);
        $collectionSellingFormat->getSelect()->columns(
            [
                'id as template_id',
                'title',
                new \Zend_Db_Expr('NULL as `account_title`'),
                new \Zend_Db_Expr('\'0\' as `account_id`'),
                new \Zend_Db_Expr(
                    '\'' . \M2E\Temu\Model\Policy\Manager::TEMPLATE_SELLING_FORMAT . '\' as `nick`'
                ),
                'create_date',
                'update_date',
            ]
        );

        // ---------------------------------------

        // Prepare synchronization collection
        // ---------------------------------------
        $collectionSynchronization = $this->syncCollectionFactory->create();
        $collectionSynchronization->getSelect()->reset(Select::COLUMNS);
        $collectionSynchronization->getSelect()->columns(
            [
                'id as template_id',
                'title',
                new \Zend_Db_Expr('NULL as `account_title`'),
                new \Zend_Db_Expr('\'0\' as `account_id`'),
                new \Zend_Db_Expr(
                    '\'' . \M2E\Temu\Model\Policy\Manager::TEMPLATE_SYNCHRONIZATION . '\' as `nick`'
                ),
                'create_date',
                'update_date',
            ]
        );

        ///Prepare Description collection
        $collectionDescription = $this->descriptionCollectionFactory->create();
        $collectionDescription->getSelect()->reset(Select::COLUMNS);
        $collectionDescription->getSelect()->columns(
            [
                'id as template_id',
                'title',
                new \Zend_Db_Expr('NULL as `account_title`'),
                new \Zend_Db_Expr('\'0\' as `account_id`'),
                new \Zend_Db_Expr(
                    '\'' . \M2E\Temu\Model\Policy\Manager::TEMPLATE_DESCRIPTION . '\' as `nick`'
                ),
                'create_date',
                'update_date',
            ]
        );

        // Prepare Shipping collection
        // ----------------------------------------
        $collectionShipping = $this->shippingCollectionFactory->create();
        $collectionShipping->getSelect()->reset(Select::COLUMNS);
        $collectionShipping->getSelect()->join(
            ['account' => $this->accountResource->getMainTable()],
            sprintf(
                'account.%s = main_table.%s',
                \M2E\Temu\Model\ResourceModel\Account::COLUMN_ID,
                \M2E\Temu\Model\ResourceModel\Policy\Shipping::COLUMN_ACCOUNT_ID
            ),
            []
        );

        $collectionShipping->getSelect()->columns(
            [
                'id as template_id',
                'title',
                new \Zend_Db_Expr('account.title as `account_title`'),
                new \Zend_Db_Expr('account.id as `account_id`'),
                new \Zend_Db_Expr(
                    '\'' . \M2E\Temu\Model\Policy\Manager::TEMPLATE_SHIPPING . '\' as `nick`'
                ),
                'create_date',
                'update_date',
            ]
        );

        // Prepare union select
        // ---------------------------------------
        $unionSelect = $this->resourceConnection->getConnection()->select();
        $unionSelect->union([
            $collectionSellingFormat->getSelect(),
            $collectionSynchronization->getSelect(),
            $collectionDescription->getSelect(),
            $collectionShipping->getSelect()
        ]);

        // Prepare result collection
        // ---------------------------------------
        $resultCollection = $this->wrapperCollectionFactory->create();
        $resultCollection->setConnection($this->resourceConnection->getConnection());
        $resultCollection->getSelect()->reset()->from(
            ['main_table' => $unionSelect],
            ['template_id', 'title',  'account_title', 'account_id','nick', 'create_date', 'update_date']
        );

        $this->setCollection($resultCollection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('title', [
            'header' => __('Title'),
            'align' => 'left',
            'type' => 'text',
            'index' => 'title',
            'escape' => true,
            'filter_index' => 'main_table.title',
        ]);

        $options = [
            \M2E\Temu\Model\Policy\Manager::TEMPLATE_SELLING_FORMAT => __('Selling'),
            \M2E\Temu\Model\Policy\Manager::TEMPLATE_SYNCHRONIZATION => __('Synchronization'),
            \M2E\Temu\Model\Policy\Manager::TEMPLATE_DESCRIPTION => __('Description'),
            \M2E\Temu\Model\Policy\Manager::TEMPLATE_SHIPPING => __('Shipping'),
        ];
        $this->addColumn('nick', [
            'header' => __('Type'),
            'align' => 'left',
            'type' => 'options',
            'width' => '100px',
            'sortable' => false,
            'index' => 'nick',
            'filter_index' => 'main_table.nick',
            'options' => $options,
        ]);

        $this->addColumn('account', [
            'header' => $this->__('Account'),
            'align' => 'left',
            'type' => 'options',
            'width' => '100px',
            'index' => 'account_title',
            'filter_index' => 'account_title',
            'filter_condition_callback' => [$this, 'callbackFilterAccount'],
            'frame_callback' => [$this, 'callbackColumnAccount'],
            'options' => $this->getEnabledAccountTitles(),
        ]);

        $this->addColumn('create_date', [
            'header' => (string)__('Creation Date'),
            'align' => 'left',
            'width' => '150px',
            'type' => 'datetime',
            'filter' => \M2E\Temu\Block\Adminhtml\Magento\Grid\Column\Filter\Datetime::class,
            'filter_time' => true,
            'format' => \IntlDateFormatter::MEDIUM,
            'index' => 'create_date',
            'filter_index' => 'main_table.create_date',
        ]);

        $this->addColumn('update_date', [
            'header' => (string)__('Update Date'),
            'align' => 'left',
            'width' => '150px',
            'type' => 'datetime',
            'filter' => \M2E\Temu\Block\Adminhtml\Magento\Grid\Column\Filter\Datetime::class,
            'filter_time' => true,
            'format' => \IntlDateFormatter::MEDIUM,
            'index' => 'update_date',
            'filter_index' => 'main_table.update_date',
        ]);

        $this->addColumn('actions', [
            'header' => __('Actions'),
            'align' => 'left',
            'width' => '100px',
            'type' => 'action',
            'index' => 'actions',
            'filter' => false,
            'sortable' => false,
            'renderer' => \M2E\Temu\Block\Adminhtml\Magento\Grid\Column\Renderer\Action::class,
            'getter' => 'getTemplateId',
            'actions' => [
                [
                    'caption' => __('Edit'),
                    'url' => [
                        'base' => '*/policy/edit',
                        'params' => [
                            'nick' => '$nick',
                        ],
                    ],
                    'field' => 'id',
                ],
                [
                    'caption' => __('Delete'),
                    'class' => 'action-default scalable add primary policy-delete-btn',
                    'url' => [
                        'base' => '*/policy/delete',
                        'params' => [
                            'nick' => '$nick',
                        ],
                    ],
                    'field' => 'id',
                    'confirm' => __('Are you sure?'),
                ],
            ],
        ]);

        return parent::_prepareColumns();
    }

    protected function callbackFilterAccount($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value == null) {
            return;
        }

        $collection->getSelect()->where('account_id = 0 OR account_id = ?', (int)$value);
    }

    public function callbackColumnAccount($value, $row, $column, $isExport)
    {
        if (empty($value)) {
            return __('Any');
        }

        return $value;
    }

    private function getEnabledAccountCollection(): AccountResource\Collection
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->accountCollection)) {
            $collection = $this->accountCollectionFactory->create();
            $collection->setOrder(AccountResource::COLUMN_TITLE, 'ASC');

            $this->accountCollection = $collection;
        }

        return $this->accountCollection;
    }

    private function getEnabledAccountTitles(): array
    {
        $result = [];
        foreach ($this->getEnabledAccountCollection()->getItems() as $account) {
            $result[$account->getId()] = $account->getTitle();
        }

        return $result;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/templateGrid', ['_current' => true]);
    }

    public function getRowUrl($item)
    {
        return $this->getUrl(
            '*/policy/edit',
            [
                'id' => $item->getData('template_id'),
                'nick' => $item->getData('nick'),
                'back' => 1,
            ]
        );
    }
}
