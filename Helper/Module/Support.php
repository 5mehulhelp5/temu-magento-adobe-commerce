<?php

declare(strict_types=1);

namespace M2E\Temu\Helper\Module;

class Support
{
    private \M2E\Temu\Model\Module $module;
    private \M2E\Core\Helper\Module\Support $supportHelper;

    public function __construct(
        \M2E\Core\Helper\Module\Support $supportHelper,
        \M2E\Temu\Model\Module $module
    ) {
        $this->module = $module;
        $this->supportHelper = $supportHelper;
    }

    public function getSummaryInfo(): string
    {
        return $this->supportHelper->getSummaryInfo($this->module);
    }
}
