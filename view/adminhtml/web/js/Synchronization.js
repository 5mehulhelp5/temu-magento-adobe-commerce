define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'M2ECore/Plugin/Messages',
    'Temu/Common'
], function (jQuery, modal, MessageObj) {

    window.Synchronization = Class.create(Common, {

        // ---------------------------------------

        saveSettings: function () {
            MessageObj.clear();
            CommonObj.scrollPageToTop();

            new Ajax.Request(Temu.url.get('synch_formSubmit'), {
                method: 'post',
                parameters: {
                    instructions_mode: $('instructions_mode').value
                },
                asynchronous: true,
                onSuccess: function (transport) {
                    MessageObj.addSuccess(Temu.translator.translate('Synchronization Settings have been saved.'));
                }
            });
        }

        // ---------------------------------------
    });
});
