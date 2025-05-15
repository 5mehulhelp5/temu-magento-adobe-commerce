<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Update\y25_m04;

class RemoveAttributeMappingTable extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $mappingTable = $this->getFullTableName('m2e_temu_attribute_mapping');
        $this->getConnection()->dropTable($mappingTable);
    }
}
