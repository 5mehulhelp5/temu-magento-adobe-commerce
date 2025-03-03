<?php

namespace M2E\Temu\Model\Wizard;

use M2E\Temu\Model\Wizard;

class InstallationTemu extends Wizard
{
    /** @var string[] */
    protected $steps = [
        'registration',
        'account',
        'settings',
        'listingTutorial',
    ];

    /**
     * @return string
     */
    public function getNick()
    {
        return \M2E\Temu\Helper\View\Temu::WIZARD_INSTALLATION_NICK;
    }
}
