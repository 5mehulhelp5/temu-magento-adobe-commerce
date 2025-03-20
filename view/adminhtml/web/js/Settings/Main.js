define([
    'mage/translate',
    'Temu/Common'
], function ($t) {
    window.TemuSettingsMain = Class.create(Common, {

        identifierSettings: {
            valueModeAttribute: ''
        },

        initialize: function (settings) {
            const self = this

            this.identifierSettings.valueModeAttribute = settings.identifierSettings.valueModeAttribute;

            jQuery.validator.addMethod('validator-required-when-visible', function (value, el) {
                return value > 0;
            }, 'This is a required field.');

            const weightValidator = function (value, el) {
                if (self.isElementHiddenFromPage(el)) {
                    return true;
                }

                if (typeof value === 'string') {
                    value = value.trim();
                }

                return new RegExp(/^(?:[0-9]*[.])?[0-9]+$/).test(value);
            }

            const sizeValidator = function (value, el) {
                if (self.isElementHiddenFromPage(el)) {
                    return true;
                }

                if (typeof value === 'string') {
                    value = value.trim();
                }

                return new RegExp(/^[0-9]+$/).test(value);
            }

            jQuery.validator.addMethod('validator-temu-weight', weightValidator, $t('The package weight must be a positive number'))
            jQuery.validator.addMethod('validator-temu-length', sizeValidator, $t('The package length needs to be a whole number that\'s not negative'))
            jQuery.validator.addMethod('validator-temu-width', sizeValidator, $t('The package width needs to be a whole number that\'s not negative'))
            jQuery.validator.addMethod('validator-temu-height', sizeValidator, $t('The package height needs to be a whole number that\'s not negative'))

            this.initObservers();
        },

        initObservers: function () {
            const self = this;

            $('identifier_code_mode').addEventListener('change', function () {
                self.identifier_code_mode_change(this);
            });

            $('package_weight_mode').addEventListener('change', function () {
                self.package_mode_change(this, $('package_weight_custom_attribute'), $('package_weight_custom_value'))
            });

            $('package_length_mode').addEventListener('change', function () {
                self.package_mode_change(this, $('package_length_custom_attribute'), $('package_length_custom_value'))
            });

            $('package_width_mode').addEventListener('change', function () {
                self.package_mode_change(this, $('package_width_custom_attribute'), $('package_width_custom_value'))
            });

            $('package_height_mode').addEventListener('change', function () {
                self.package_mode_change(this, $('package_height_custom_attribute'), $('package_height_custom_value'))
            });
        },

        // ---------------------------------------

        identifier_code_mode_change: function (option) {
            $('identifier_code_custom_attribute').value = '';
            if (option.value == this.identifierSettings.valueModeAttribute) {
                this.updateHiddenValue(option, $('identifier_code_custom_attribute'));
            }
        },

        package_mode_change: function (option, customAttributeInput, customValueInput) {
            customAttributeInput.value = ''
            customValueInput.value = ''
            customValueInput.hide()

            // if (option.value == Temu.php.constant('M2E_Temu_Helper_Component_Temu_Configuration::PACKAGE_MODE_CUSTOM_ATTRIBUTE')) {
            if (option.value == Temu.php.constant('M2E_Temu_Model_Settings::PACKAGE_MODE_CUSTOM_ATTRIBUTE')) {
                this.updateHiddenValue(option, customAttributeInput);
            }

            if (option.value == Temu.php.constant('M2E_Temu_Model_Settings::PACKAGE_MODE_CUSTOM_VALUE')) {
                customValueInput.show();
            }
        },
    });
});
