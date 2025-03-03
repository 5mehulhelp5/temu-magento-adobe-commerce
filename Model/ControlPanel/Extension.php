<?php

declare(strict_types=1);

namespace M2E\Temu\Model\ControlPanel;

class Extension implements \M2E\Core\Model\ControlPanel\ExtensionInterface
{
    public const NAME = 'm2e_temu';

    private \M2E\Temu\Model\Module $module;

    private array $tabs;

    public function __construct(
        \M2E\Temu\Model\Module $module
    ) {
        $this->module = $module;
    }

    public function getIdentifier(): string
    {
        return \M2E\Temu\Helper\Module::IDENTIFIER;
    }

    public function getModule(): \M2E\Core\Model\ModuleInterface
    {
        return $this->module;
    }

    public function getModuleName(): string
    {
        return self::NAME;
    }

    public function getModuleTitle(): string
    {
        return 'M2E Temu';
    }
}
