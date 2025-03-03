define([
    'Magento_Ui/js/modal/modal',
    'Temu/Common'
], function (modal) {

    window.Log = Class.create(Common, {

        // ---------------------------------------

        initialize: function () {
        },

        // ---------------------------------------

        showFullText: function (element) {
            var content = '<div class="log-description-full">' +
                    element.next().innerHTML +
                    '</div>';

            modal({
                title: Temu.translator.translate('Description'),
                type: 'popup',
                modalClass: 'width-1000',
                buttons: [{
                    text: Temu.translator.translate('Close'),
                    class: 'action-secondary',
                    click: function () {
                        this.closeModal();
                    }
                }]
            }, content).openModal();
        }

        // ---------------------------------------
    });
});
