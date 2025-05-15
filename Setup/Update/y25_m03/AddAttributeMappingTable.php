<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Update\y25_m03;

use Magento\Framework\DB\Ddl\Table;

class AddAttributeMappingTable extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $table = $this
            ->getConnection()
            ->newTable($this->getFullTableName('m2e_temu_attribute_mapping'));

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
                'type',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false]
            )
            ->addColumn(
                'channel_attribute_title',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'channel_attribute_code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'magento_attribute_code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'update_date',
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addColumn(
                'create_date',
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addIndex('type', 'type')
            ->addIndex('create_date', 'create_date')
            ->setOption('type', 'INNODB')
            ->setOption('charset', 'utf8')
            ->setOption('collate', 'utf8_general_ci')
            ->setOption('row_format', 'dynamic');

        $this->getConnection()->createTable($table);
    }
}
