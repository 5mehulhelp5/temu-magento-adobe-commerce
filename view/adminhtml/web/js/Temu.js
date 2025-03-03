define([
    'jquery',
    'Temu/Url',
    'Temu/Php',
    'Temu/Translator',
    'Temu/Common',
    'prototype',
    'Temu/Plugin/BlockNotice',
    'Temu/Plugin/Prototype/Event.Simulate',
    'Temu/Plugin/Fieldset',
    'Temu/Plugin/Validator',
    'Temu/General/PhpFunctions',
    'mage/loader_old'
], function (jQuery, Url, Php, Translator) {

    jQuery('body').loader();

    Ajax.Responders.register({
        onException: function (event, error) {
            console.error(error);
        }
    });

    return {
        url: Url,
        php: Php,
        translator: Translator
    };

});
