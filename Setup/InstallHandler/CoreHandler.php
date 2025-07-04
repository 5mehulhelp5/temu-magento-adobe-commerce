<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\InstallHandler;

use M2E\Temu\Helper\Module\Database\Tables as TablesHelper;
use M2E\Temu\Model\Module\Configuration;
use M2E\Temu\Model\ResourceModel\Lock\Item as LockItemResource;
use M2E\Temu\Model\ResourceModel\Lock\Transactional as LockTransactionalResource;
use M2E\Temu\Model\ResourceModel\OperationHistory as OperationHistoryResource;
use Magento\Framework\DB\Ddl\Table;
use M2E\Temu\Model\Cron\Config as CronConfig;
use M2E\Temu\Model\Cron\Task\System\Servicing\SynchronizeTask as CronTaskServicing;

class CoreHandler implements \M2E\Core\Model\Setup\InstallHandlerInterface
{
    private \M2E\Core\Helper\Module\Database\Tables $tablesHelper;
    private \M2E\Core\Model\Setup\Database\Modifier\ConfigFactory $configFactory;

    public function __construct(
        \M2E\Core\Helper\Module\Database\Tables $tablesHelper,
        \M2E\Core\Model\Setup\Database\Modifier\ConfigFactory $configFactory
    ) {
        $this->tablesHelper = $tablesHelper;
        $this->configFactory = $configFactory;
    }

    public function installSchema(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $this->installWizardTable($setup);
        $this->installOperationHistoryTable($setup);
        $this->installLockItemTable($setup);
        $this->installLockTransactionalTable($setup);
    }

    private function installWizardTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $tableName = $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_WIZARD);

        $table = $setup->getConnection()->newTable($tableName);

        $table
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'primary' => true,
                    'nullable' => false,
                    'auto_increment' => true,
                ]
            )
            ->addColumn(
                'nick',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'view',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'status',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'step',
                Table::TYPE_TEXT,
                255,
                ['default' => null]
            )
            ->addColumn(
                'type',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'priority',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addIndex('nick', 'nick')
            ->setOption('type', 'INNODB')
            ->setOption('charset', 'utf8')
            ->setOption('collate', 'utf8_general_ci')
            ->setOption('row_format', 'dynamic');

        $setup->getConnection()->createTable($table);
    }

    private function installOperationHistoryTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $tableName = $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_OPERATION_HISTORY);

        $table = $setup->getConnection()->newTable($tableName);

        $table
            ->addColumn(
                OperationHistoryResource::COLUMN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'primary' => true,
                    'nullable' => false,
                    'auto_increment' => true,
                ]
            )
            ->addColumn(
                OperationHistoryResource::COLUMN_NICK,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                OperationHistoryResource::COLUMN_PARENT_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'default' => null]
            )
            ->addColumn(
                OperationHistoryResource::COLUMN_INITIATOR,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0]
            )
            ->addColumn(
                OperationHistoryResource::COLUMN_START_DATE,
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                OperationHistoryResource::COLUMN_END_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addColumn(
                OperationHistoryResource::COLUMN_DATA,
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                OperationHistoryResource::COLUMN_UPDATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addColumn(
                OperationHistoryResource::COLUMN_CREATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addIndex('nick', OperationHistoryResource::COLUMN_NICK)
            ->addIndex('parent_id', OperationHistoryResource::COLUMN_PARENT_ID)
            ->addIndex('initiator', OperationHistoryResource::COLUMN_INITIATOR)
            ->addIndex('start_date', OperationHistoryResource::COLUMN_START_DATE)
            ->addIndex('end_date', OperationHistoryResource::COLUMN_END_DATE)
            ->setOption('type', 'INNODB')
            ->setOption('charset', 'utf8')
            ->setOption('collate', 'utf8_general_ci')
            ->setOption('row_format', 'dynamic');

        $setup->getConnection()->createTable($table);
    }

    private function installLockItemTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $tableName = $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_LOCK_ITEM);

        $table = $setup->getConnection()->newTable($tableName);

        $table
            ->addColumn(
                LockItemResource::COLUMN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'primary' => true,
                    'nullable' => false,
                    'auto_increment' => true,
                ]
            )
            ->addColumn(
                LockItemResource::COLUMN_NICK,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                LockItemResource::COLUMN_PARENT_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'default' => null]
            )
            ->addColumn(
                LockItemResource::COLUMN_DATA,
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                LockItemResource::COLUMN_UPDATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addColumn(
                LockItemResource::COLUMN_CREATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addIndex('nick', LockItemResource::COLUMN_NICK)
            ->addIndex('parent_id', LockItemResource::COLUMN_PARENT_ID)
            ->setOption('type', 'INNODB')
            ->setOption('charset', 'utf8')
            ->setOption('collate', 'utf8_general_ci')
            ->setOption('row_format', 'dynamic');

        $setup->getConnection()->createTable($table);
    }

    private function installLockTransactionalTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $tableName = $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_LOCK_TRANSACTIONAL);

        $table = $setup->getConnection()->newTable($tableName);

        $table
            ->addColumn(
                LockTransactionalResource::COLUMN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'primary' => true,
                    'nullable' => false,
                    'auto_increment' => true,
                ]
            )
            ->addColumn(
                LockTransactionalResource::COLUMN_NICK,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                LockTransactionalResource::COLUMN_CREATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addIndex('nick', LockTransactionalResource::COLUMN_NICK)
            ->setOption('type', 'INNODB')
            ->setOption('charset', 'utf8')
            ->setOption('collate', 'utf8_general_ci')
            ->setOption('row_format', 'dynamic');

        $setup->getConnection()->createTable($table);
    }

    public function installData(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $this->installConfigData($setup);
        $this->installWizardData($setup);
    }

    private function installWizardData(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $tableName = $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_WIZARD);

        $insertData = [
            [
                'nick' => 'installationTemu',
                'view' => 'temu',
                'status' => 0,
                'step' => null,
                'type' => 1,
                'priority' => 2,
            ],
        ];

        $setup->getConnection()->insertMultiple($tableName, $insertData);
    }

    private function installConfigData(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $config = $this->configFactory->create(
            \M2E\Temu\Helper\Module::IDENTIFIER,
            $setup
        );

        $config->insert('/', 'is_disabled', '0');
        $config->insert('/', 'environment', 'production');
        $config->insert(
            \M2E\Temu\Model\Connector\Client\Config::CONFIG_GROUP_SERVER,
            \M2E\Temu\Model\Connector\Client\Config::CONFIG_KEY_APPLICATION_KEY,
            'ba388880611dd3f622cd97399486cc1f7c54e62f'
        );
        $config->insert(CronConfig::CONFIG_GROUP, CronConfig::CONFIG_KEY_MODE, '1');
        $config->insert(CronConfig::CONFIG_GROUP, CronConfig::CONFIG_KEY_RUNNER, CronConfig::RUNNER_MAGENTO);
        $config->insert(CronConfig::getRunnerConfigGroup(CronConfig::RUNNER_MAGENTO), CronConfig::CONFIG_KEY_RUNNER_DISABLED, '0');
        $config->insert(CronConfig::getTaskConfigGroup(CronTaskServicing::NICK), CronConfig::CONFIG_KEY_TASK_INTERVAL, random_int(43200, 86400));
        $config->insert('/logs/clearing/listings/', 'mode', '1');
        $config->insert('/logs/clearing/listings/', 'days', '30');
        $config->insert('/logs/clearing/synchronizations/', 'mode', '1');
        $config->insert('/logs/clearing/synchronizations/', 'days', '30');
        $config->insert('/logs/clearing/orders/', 'mode', '1');
        $config->insert('/logs/clearing/orders/', 'days', '90');
        $config->insert('/logs/listings/', 'last_action_id', '0');
        $config->insert('/logs/grouped/', 'max_records_count', '100000');
        $config->insert('/support/', 'contact_email', 'support@m2epro.com');
        $config->insert(
            \M2E\Temu\Model\Settings::CONFIG_GROUP,
            \M2E\Temu\Model\Settings::CONFIG_KEY_IDENTIFIER_CODE_MODE,
            \M2E\Temu\Model\Settings::VALUE_MODE_NOT_SET
        );
        $config->insert(
            \M2E\Temu\Model\Settings::CONFIG_GROUP,
            \M2E\Temu\Model\Settings::CONFIG_KEY_IDENTIFIER_CODE_CUSTOM_ATTRIBUTE,
            ''
        );
        $config->insert(Configuration::CONFIG_GROUP, 'listing_product_inspector_mode', '0');
        $config->insert(Configuration::CONFIG_GROUP, 'view_show_block_notices_mode', '1');
        $config->insert(Configuration::CONFIG_GROUP, 'view_show_products_thumbnails_mode', '1');
        $config->insert(Configuration::CONFIG_GROUP, 'view_products_grid_use_alternative_mysql_select_mode', '0');
        $config->insert(Configuration::CONFIG_GROUP, 'other_pay_pal_url', 'paypal.com/cgi-bin/webscr/');
        $config->insert(Configuration::CONFIG_GROUP, 'product_index_mode', '1');
        $config->insert(Configuration::CONFIG_GROUP, 'product_force_qty_mode', '0');
        $config->insert(Configuration::CONFIG_GROUP, 'product_force_qty_value', '10');
        $config->insert(Configuration::CONFIG_GROUP, 'qty_percentage_rounding_greater', '0');
        $config->insert(Configuration::CONFIG_GROUP, 'magento_attribute_price_type_converting_mode', '0');
        $config->insert(
            Configuration::CONFIG_GROUP,
            'create_with_first_product_options_when_variation_unavailable',
            '1'
        );
        $config->insert(Configuration::CONFIG_GROUP, 'secure_image_url_in_item_description_mode', '0');
        $config->insert('/magento/product/simple_type/', 'custom_types', '');
        $config->insert('/magento/product/downloadable_type/', 'custom_types', '');
        $config->insert('/magento/product/configurable_type/', 'custom_types', '');
        $config->insert('/magento/product/bundle_type/', 'custom_types', '');
        $config->insert('/magento/product/grouped_type/', 'custom_types', '');
        $config->insert('/health_status/notification/', 'mode', 1);
        $config->insert('/health_status/notification/', 'email', '');
        $config->insert('/health_status/notification/', 'level', 40);
        $config->insert(
            \M2E\Temu\Model\Product\InspectDirectChanges\Config::GROUP,
            \M2E\Temu\Model\Product\InspectDirectChanges\Config::KEY_MAX_ALLOWED_PRODUCT_COUNT,
            '2000'
        );
        $config->insert('/listing/product/instructions/cron/', 'listings_products_per_one_time', '1000');
        $config->insert('/listing/product/scheduled_actions/', 'max_prepared_actions_count', '3000');
    }
}
