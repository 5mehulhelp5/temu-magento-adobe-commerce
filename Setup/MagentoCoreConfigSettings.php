<?php

declare(strict_types=1);

namespace M2E\Temu\Setup;

class MagentoCoreConfigSettings implements \M2E\Core\Model\Setup\MagentoCoreConfigSettingsInterface
{
    public function getConfigKeyPrefix(): string
    {
        return \M2E\Temu\Helper\Module\Database\Tables::PREFIX;
    }
}
