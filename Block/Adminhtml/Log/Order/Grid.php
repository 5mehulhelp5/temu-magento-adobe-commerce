<?php

namespace M2E\Temu\Block\Adminhtml\Log\Order;

use M2E\Temu\Model\ResourceModel\Order as OrderResource;

class Grid extends \M2E\Temu\Block\Adminhtml\Log\AbstractGrid
{
    private \M2E\Core\Helper\Module\Database\Structure $databaseHelper;
    private \M2E\Temu\Model\ResourceModel\Order\Log\CollectionFactory $logCollectionFactory;
    private \M2E\Temu\Model\ResourceModel\Account $accountResource;
    private \M2E\Temu\Model\ResourceModel\Order $orderResource;

    public function __construct(
        \M2E\Temu\Model\ResourceModel\Order $orderResource,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \M2E\Temu\Helper\View $viewHelper,
        \M2E\Temu\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \M2E\Core\Helper\Module\Database\Structure $databaseHelper,
        \M2E\Temu\Model\ResourceModel\Order\Log\CollectionFactory $logCollectionFactory,
        \M2E\Temu\Model\ResourceModel\Account $accountResource,
        array $data = []
    ) {
        $this->databaseHelper = $databaseHelper;
        $this->logCollectionFactory = $logCollectionFactory;
        $this->accountResource = $accountResource;
        $this->orderResource = $orderResource;
        parent::__construct(
            $resourceConnection,
            $viewHelper,
            $context,
            $backendHelper,
            $data
        );
    }

    public function _construct()
    {
        parent::_construct();

        $this->css->addFile('order/log/grid.css');

        $this->setId('temuOrderLogGrid');

        $this->setDefaultSort('create_date');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setCustomPageSize(true);

        $this->entityIdFieldName = self::ORDER_ID_FIELD;
        $this->logModelName = 'Order_Log';
    }

    protected function _prepareCollection()
    {
        $collection = $this->logCollectionFactory->create();

        $isNeedCombine = $this->isNeedCombineMessages();

        if ($isNeedCombine) {
            $collection->getSelect()->columns(
                ['create_date' => new \Zend_Db_Expr('MAX(main_table.create_date)')]
            );
            $collection->getSelect()->group(['main_table.order_id', 'main_table.description']);
        }

        $collection->getSelect()->joinLeft(
            ['mo' => $this->orderResource->getMainTable()],
            '(mo.id = `main_table`.order_id)',
            [
                'magento_order_id' => 'magento_order_id',
                'channel_order_id' => 'channel_order_id',
            ]
        );

        $accountId = (int)$this->getRequest()->getParam('account', false);

        if ($accountId) {
            $collection->addFieldToFilter('main_table.account_id', $accountId);
        } else {
            $collection->getSelect()->joinLeft(
                ['account_table' => $this->accountResource->getMainTable()],
                'main_table.account_id = account_table.id',
                ['real_account_id' => 'account_table.id']
            );
            $collection->addFieldToFilter('account_table.id', ['notnull' => true]);
        }

        $collection->getSelect()->joinLeft(
            ['so' => $this->databaseHelper->getTableNameWithPrefix('sales_order')],
            '(so.entity_id = `mo`.magento_order_id)',
            ['magento_order_number' => 'increment_id']
        );

        $orderId = $this->getRequest()->getParam('id', false);

        if ($orderId) {
            $collection->addFieldToFilter('main_table.order_id', (int)$orderId);
        }

        $backToDate = \M2E\Core\Helper\Date::createCurrentGmt();
        $backToDate->modify('- 1 days');

        if ($this->getRequest()->getParam('magento_order_failed')) {
            $text = 'Magento Order was not created';
            $collection->addFieldToFilter('main_table.description', ['like' => '%' . $text . '%']);
            $collection->addFieldToFilter('mo.magento_order_creation_latest_attempt_date', [
                'gteq' => $backToDate->format('Y-m-d H:i:s'),
            ]);
        }

        $this->setCollection($collection);
        $result = parent::_prepareCollection();

        if ($isNeedCombine) {
            $this->prepareMessageCount($collection);
        }

        return $result;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('create_date', [
            'header' => (string)__('Creation Date'),
            'align' => 'left',
            'type' => 'datetime',
            'filter' => \M2E\Temu\Block\Adminhtml\Magento\Grid\Column\Filter\Datetime::class,
            'filter_time' => true,
            'index' => 'create_date',
            'filter_index' => 'main_table.create_date',
        ]);

        $this->addColumn('channel_order_id', [
            'header' => __(\M2E\Temu\Helper\Module::getChannelTitle() . ' Order #'),
            'align' => 'left',
            'sortable' => false,
            'index' => 'channel_order_id',
            'frame_callback' => [$this, 'callbackColumnChannelOrderId'],
            'filter_condition_callback' => [$this, 'callbackFilterChannelOrderId'],
        ]);

        $this->addColumn('magento_order_number', [
            'header' => __('Magento Order #'),
            'align' => 'left',
            'index' => 'so.increment_id',
            'sortable' => false,
            'frame_callback' => [$this, 'callbackColumnMagentoOrderNumber'],
        ]);

        $this->addColumn('description', [
            'header' => __('Message'),
            'align' => 'left',
            'index' => 'description',
            'frame_callback' => [$this, 'callbackColumnDescription'],
        ]);

        $this->addColumn('initiator', [
            'header' => __('Run Mode'),
            'align' => 'right',
            'index' => 'initiator',
            'sortable' => false,
            'type' => 'options',
            'options' => $this->_getLogInitiatorList(),
            'frame_callback' => [$this, 'callbackColumnInitiator'],
        ]);

        $this->addColumn('type', [
            'header' => __('Type'),
            'align' => 'right',
            'index' => 'type',
            'type' => 'options',
            'sortable' => false,
            'options' => $this->_getLogTypeList(),
            'frame_callback' => [$this, 'callbackColumnType'],
        ]);

        return parent::_prepareColumns();
    }

    public function callbackColumnChannelOrderId($value, $row, $column, $isExport)
    {
        $url = $this->getUrl('*/order/view', ['id' => $row->getData('order_id')]);

        return '<a href="' . $url . '" target="_blank">' . \M2E\Core\Helper\Data::escapeHtml(
            $row->getData(OrderResource::COLUMN_CHANNEL_ORDER_ID)
        ) . '</a>';
    }

    public function callbackColumnMagentoOrderNumber($value, $row, $column, $isExport)
    {
        $magentoOrderId = $row->getData(OrderResource::COLUMN_MAGENTO_ORDER_ID);
        $magentoOrderNumber = $row->getData('magento_order_number');

        if (!$magentoOrderId) {
            $result = __('N/A');
        } else {
            $url = $this->getUrl('sales/order/view', ['order_id' => $magentoOrderId]);
            $result = '<a href="' . $url . '" target="_blank">'
                . \M2E\Core\Helper\Data::escapeHtml($magentoOrderNumber) . '</a>';
        }

        return "<span style='min-width: 110px; display: block;'>{$result}</span>";
    }

    public function callbackFilterChannelOrderId($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value == null) {
            return;
        }

        $collection->addFieldToFilter(OrderResource::COLUMN_CHANNEL_ORDER_ID, ['like' => "%$value%"]);
    }

    public function getRowUrl($item)
    {
        return false;
    }
}
