<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Update\y25_m03;

use M2E\Temu\Helper\Module\Database\Tables;
use M2E\Temu\Model\ResourceModel\Account;

class AddRegionToAccount extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    private const SITE_ID_US = 100;

    public function execute(): void
    {
        $this->createColumn();
        $this->updateColumn();
    }

    private function createColumn(): void
    {
        $modifier = $this->createTableModifier(Tables::TABLE_NAME_ACCOUNT);

        $modifier->addColumn(
            Account::COLUMN_REGION,
            'VARCHAR(255) NOT NULL',
            null,
            Account::COLUMN_SITE_TITLE,
            false,
            false
        );

        $modifier->commit();
    }

    private function updateColumn(): void
    {
        $this->getConnection()->update(
            $this->getFullTableName(Tables::TABLE_NAME_ACCOUNT),
            ['region' => new \Zend_Db_Expr("CASE WHEN site_id = " . self::SITE_ID_US . " THEN 'US' ELSE 'EU' END")]
        );
    }
}
