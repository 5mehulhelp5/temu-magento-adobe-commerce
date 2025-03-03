define([
    'M2ECore/Plugin/Messages',
], function (MessageObj) {

    window.WizardInstallationTemu = Class.create(Common, {

        continueStep: function () {
            if (WizardObj.steps.current.length) {
                this[WizardObj.steps.current + 'Step']();
            }
        },

        // Steps
        // ---------------------------------------

        registrationStep: function () {
            WizardObj.registrationStep(Temu.url.get('wizard_registration/createLicense'));
        },

        accountStep: function () {
            if (!this.isValidForm()) {
                return false;
            }

            new Ajax.Request(Temu.url.get('wizard_installationTemu/beforeToken'), {
                method: 'post',
                asynchronous: true,
                parameters: $('edit_form').serialize(),
                onSuccess: function (transport) {

                    var response = transport.responseText.evalJSON();

                    if (response && response['message']) {
                        MessageObj.addError(response['message']);
                        return CommonObj.scrollPageToTop();
                    }

                    if (!response['url']) {
                        MessageObj.addError(Temu.translator.translate('An error during of account creation.'));
                        return CommonObj.scrollPageToTop();
                    }

                    return setLocation(response['url']);
                }
            });
        },

        settingsStep: function () {
            this.initFormValidation();

            if (!this.isValidForm()) {
                return false;
            }

            this.submitForm(Temu.url.get('wizard_installationTemu/settingsContinue'));
        },

        listingTutorialStep: function () {
            WizardObj.setStep(WizardObj.getNextStep(), function () {
                WizardObj.complete();
            });
        }

        // ---------------------------------------
    });
});
