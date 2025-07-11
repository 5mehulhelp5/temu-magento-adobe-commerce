define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/modal'
], function (jQuery, confirm, modal) {

    window.ListingEditListingTitle = Class.create({

        // ---------------------------------------

        initialize: function (gridId) {
            this.gridId = gridId;

            CommonObj.setValidationCheckRepetitionValue(
                    'Temu-listing-title',
                    Temu.translator.translate('The specified Title is already used for other Listing. Listing Title must be unique.'),
                    'Listing', 'title', 'id', null
            );
        },

        openPopup: function (id) {
            var self = this;

            new Ajax.Request(Temu.url.get('listing_edit/title'), {
                method: 'GET',
                parameters: {
                    id: id
                },
                onSuccess: (function (transport) {
                    if ($('edit_form')) {
                        $('edit_form').remove();
                    }

                    $('html-body').insert({bottom: transport.responseText});

                    var form = jQuery('#edit_form');

                    modal({
                        title: Temu.translator.translate('Edit Listing Title'),
                        type: 'popup',
                        modalClass: 'width-50',
                        buttons: [{
                            text: Temu.translator.translate('Cancel'),
                            class: 'action-secondary action-dismiss',
                            click: function () {
                                form.modal('closeModal');
                            }
                        }, {
                            text: Temu.translator.translate('Save'),
                            class: 'action-primary action-accept',
                            click: function () {
                                EditListingTitleObj.saveListingTitle(id);
                            }
                        }]
                    }, form);

                    self.oldTitle = form.find('#title').val();
                    jQuery('#edit_form').modal('openModal');
                }).bind(this)
            });
        },

        saveListingTitle: function () {
            var self = this,
                    form = $('edit_form'),
                    newTitle = form.select('#title')[0].value;

            if (self.oldTitle == newTitle) {
                jQuery('#edit_form').modal('closeModal');
                return;
            }

            if (!jQuery('#edit_form').valid()) {
                return false;
            }

            confirm({
                content: Temu.translator.translate('Are you sure?'),
                actions: {
                    confirm: function () {
                        new Ajax.Request(Temu.url.get('listing_edit/title'), {
                            parameters: $('edit_form').serialize(true),
                            onSuccess: (function (transport) {
                                jQuery('#edit_form').modal('closeModal');
                                window[self.gridId + 'JsObject'].reload();
                            })
                        });
                    },
                    cancel: function () {
                        jQuery('#edit_form').modal('closeModal');
                        return false;
                    }
                }
            });
        }
    });
});
