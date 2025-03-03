<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ResourceModel;

class Wizard extends ActiveRecord\AbstractModel
{
    public function _construct(): void
    {
        $this->_init(\M2E\Temu\Helper\Module\Database\Tables::TABLE_NAME_WIZARD, 'id');
    }
}
