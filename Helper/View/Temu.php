<?php

namespace M2E\Temu\Helper\View;

class Temu
{
    public const NICK = 'temu';

    public const WIZARD_INSTALLATION_NICK = 'installationTemu';
    public const MENU_ROOT_NODE_NICK = 'M2E_Temu::temu';

    private \M2E\Temu\Helper\Module\Wizard $wizardHelper;

    public function __construct(
        \M2E\Temu\Helper\Module\Wizard $wizardHelper
    ) {
        $this->wizardHelper = $wizardHelper;
    }

    // ----------------------------------------

    public static function getWizardInstallationNick(): string
    {
        return self::WIZARD_INSTALLATION_NICK;
    }

    public function isInstallationWizardFinished(): bool
    {
        return $this->wizardHelper->isFinished(
            self::getWizardInstallationNick()
        );
    }
}
