<?php

namespace M2E\Temu\Block\Adminhtml\Wizard\InstallationTemu;

abstract class Installation extends \M2E\Temu\Block\Adminhtml\Wizard\Installation
{
    protected function _construct()
    {
        parent::_construct();

        $this->updateButton('continue', 'onclick', 'InstallationWizardObj.continueStep();');
    }

    protected function _toHtml()
    {
        $this->js->add(
            <<<JS
    require([
        'Temu/Wizard/InstallationTemu',
    ], function(){
        window.InstallationWizardObj = new WizardInstallationTemu();
    });
JS
        );

        return parent::_toHtml();
    }
}
