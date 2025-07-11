define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'M2ECore/Plugin/Messages',
    'Temu/Action',
    'Temu/Plugin/ProgressBar',
    'Temu/Plugin/AreaWrapper'
], function (jQuery, modal, MessagesObj) {
    window.ListingMoving = Class.create(Action, {

        // ---------------------------------------

        setProgressBar: function (progressBarId) {
            this.progressBarObj = new ProgressBar(progressBarId);
        },

        setGridWrapper: function (wrapperId) {
            this.wrapperObj = new AreaWrapper(wrapperId);
        },

        // ---------------------------------------

        run: function () {
            this.getGridHtml(this.gridHandler.getSelectedProductsArray());
        },

        // ---------------------------------------

        openPopUp: function (gridHtml, popup_title, buttons) {
            var self = this;

            if (typeof buttons === 'undefined') {
                buttons = [{
                    class: 'action-secondary action-dismiss',
                    text: Temu.translator.translate('Cancel'),
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: Temu.translator.translate('Add New Listing'),
                    class: 'action-primary action-accept',
                    click: function () {
                        self.startListingCreation(Temu.url.get('add_new_listing_url'));
                    }
                }];
            }

            var modalDialogMessage = $('move_modal_dialog_message');

            if (modalDialogMessage) {
                modalDialogMessage.remove();
            }

            modalDialogMessage = new Element('div', {
                id: 'move_modal_dialog_message'
            });

            modalDialogMessage.update(gridHtml);

            this.popUp = jQuery(modalDialogMessage).modal({
                title: popup_title,
                type: 'popup',
                buttons: buttons
            });

            this.popUp.modal('openModal');
        },

        // ---------------------------------------

        getGridHtml: function (selectedProducts) {
            this.selectedProducts = selectedProducts;
            this.gridHandler.unselectAll();
            MessagesObj.clear();
            $('listing_container_errors_summary').hide();

            this.progressBarObj.reset();
            this.progressBarObj.setTitle('Preparing for Product Moving');
            this.progressBarObj.setStatus('Products are being prepared for Moving. Please wait…');
            this.progressBarObj.show();
            this.scrollPageToTop();

            $$('.loading-mask').invoke('setStyle', {visibility: 'hidden'});
            this.wrapperObj.lock();

            var productsByParts = this.makeProductsParts();
            this.prepareData(productsByParts, productsByParts.length, 1);
        },

        makeProductsParts: function () {
            var self = this;

            var productsInPart = 500;
            var parts = [];

            if (self.selectedProducts.length < productsInPart) {
                var part = [];
                part[0] = self.selectedProducts;
                return parts[0] = part;
            }

            var result = [];
            for (var i = 0; i < self.selectedProducts.length; i++) {
                if (result.length === 0 || result[result.length - 1].length === productsInPart) {
                    result[result.length] = [];
                }
                result[result.length - 1][result[result.length - 1].length] = self.selectedProducts[i];
            }

            return result;
        },

        prepareData: function (parts, partsCount, isFirstPart) {
            var self = this;

            if (parts.length === 0) {
                return;
            }

            var isLastPart = parts.length === 1 ? 1 : 0;
            var part = parts.splice(0, 1);
            var currentPart = part[0];

            new Ajax.Request(Temu.url.get('prepareData'), {
                method: 'post',
                parameters: {
                    is_first_part: isFirstPart,
                    is_last_part: isLastPart,
                    products_part: implode(',', currentPart)
                },
                onSuccess: (function (transport) {

                    var percents = (100 / partsCount) * (partsCount - parts.length);

                    if (percents <= 0) {
                        self.progressBarObj.setPercents(0, 0);
                    } else if (percents >= 100) {
                        self.progressBarObj.setPercents(100, 0);
                        self.progressBarObj.setStatus('Products are almost prepared for Moving...');
                    } else {
                        self.progressBarObj.setPercents(percents, 1);
                    }

                    var response = transport.responseText.evalJSON();
                    if (!response.result) {

                        self.completeProgressBar();
                        if (typeof response.message !== 'undefined') {
                            MessagesObj.addError(response.message);
                        }
                        return;
                    }

                    if (isLastPart) {

                        self.accountId = response.accountId;
                        self.region = response.region;

                        self.moveToListingGrid();
                        return;
                    }

                    setTimeout(function () {
                        self.prepareData(parts, partsCount, 0);
                    }, 500);
                })
            });
        },

        moveToListingGrid: function () {
            var self = this;

            new Ajax.Request(Temu.url.get('moveToListingGridHtml'), {
                method: 'get',
                parameters: {
                    accountId: self.accountId,
                    region: self.region,
                    ignoreListings: Temu.customData.ignoreListings
                },
                onSuccess: (function (transport) {
                    self.completeProgressBar();
                    self.openPopUp(transport.responseText, Temu.translator.translate('popup_title'));
                })
            });
        },

        // ---------------------------------------

        submit: function (listingId, onSuccess) {
            var self = this;

            $$('.loading-mask').invoke('setStyle', {visibility: 'visible'});

            new Ajax.Request(Temu.url.get('createListing'), {
                method: 'post',
                parameters: {
                    listingId: listingId
                },
                onSuccess: function (transport) {

                    self.popUp.modal('closeModal');
                    self.scrollPageToTop();

                    var response = transport.responseText.evalJSON();

                    $$('.loading-mask').invoke('setStyle', {visibility: 'hidden'});
                    if (response.result) {
                        if (response.isFailed) {
                            if (response.message) {
                                MessagesObj.addError(response.message);
                            }
                        }

                        var wizardId = response.wizardId;
                        onSuccess.bind(self.gridHandler)(wizardId);

                        return;
                    }

                    self.gridHandler.unselectAllAndReload();
                    if (response.message) {
                        MessagesObj.addError(response.message);
                    }

                }
            });
        },

        // ---------------------------------------

        startListingCreation: function (url, response) {
            var win = window.open(url);

            var intervalId = setInterval(function () {
                if (!win.closed) {
                    return;
                }

                clearInterval(intervalId);

                listingMovingGridJsObject.reload();
            }, 1000);
        },

        // ---------------------------------------

        completeProgressBar: function () {
            this.progressBarObj.hide();
            this.progressBarObj.reset();
            this.wrapperObj.unlock();
            $$('.loading-mask').invoke('setStyle', {visibility: 'hidden'});
        }

        // ---------------------------------------
    });
});
