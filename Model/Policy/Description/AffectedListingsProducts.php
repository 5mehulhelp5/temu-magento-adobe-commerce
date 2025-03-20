<?php

declare(strict_types=1);

namespace M2E\Temu\Model\Policy\Description;

use M2E\Temu\Model\Policy\AffectedListingsProducts\AffectedListingsProductsAbstract;

class AffectedListingsProducts extends AffectedListingsProductsAbstract
{
    public function getTemplateNick(): string
    {
        return \M2E\Temu\Model\Policy\Manager::TEMPLATE_DESCRIPTION;
    }
}
