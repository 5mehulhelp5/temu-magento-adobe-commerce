<?php

declare(strict_types=1);

namespace M2E\Temu\Setup\Update;

class Config implements \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
{
    public function getFeaturesList(): array
    {
        return [];
    }
}
