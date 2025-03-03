define([
    'Magento_Ui/js/modal/modal',
    'M2ECore/Plugin/Messages',
    'Temu/Listing/View/Grid'
], function (modal, MessageObj) {

    window.TemuListingViewTemuGrid = Class.create(ListingViewGrid, {

        // ---------------------------------------

        afterInitPage: function ($super) {
            $super();

            $(this.gridId + '_massaction-select').observe('change', function () {
                if (!$('get-estimated-fee')) {
                    return;
                }

                if (this.value == 'list') {
                    $('get-estimated-fee').show();
                } else {
                    $('get-estimated-fee').hide();
                }
            });
        },

        // ---------------------------------------

        getMaxProductsInPart: function () {
            return 10;
        },

        // ---------------------------------------

        getLogViewUrl: function (rowId) {
            const idField = Temu.php.constant('\\M2E\\Temu\\Block\\Adminhtml\\Log\\Listing\\Product\\AbstractGrid::LISTING_PRODUCT_ID_FIELD');

            let params = {};
            params[idField] = rowId;

            return Temu.url.get('log_listing_product/index', params);
        },

        // ---------------------------------------

        openFeePopUp: function (content, title) {
            let feePopup = $('fee_popup');

            if (feePopup) {
                feePopup.remove();
            }

            $('html-body').insert({bottom: '<div id="fee_popup"></div>'});

            $('fee_popup').update(content);

            let popup = jQuery('#fee_popup');

            modal({
                title: title,
                type: 'popup',
                buttons: [{
                    text: Temu.translator.translate('Close'),
                    class: 'action-secondary',
                    click: function () {
                        popup.modal('closeModal');
                    }
                }]
            }, popup);

            popup.modal('openModal');
        },

        // ---------------------------------------
    });
});
