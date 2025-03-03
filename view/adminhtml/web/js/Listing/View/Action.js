define([
    'M2ECore/Plugin/Messages',
    'Temu/Action',
    'Temu/Plugin/ProgressBar',
    'Temu/Plugin/AreaWrapper'
], function (MessageObj) {

    window.ListingViewAction = Class.create(Action, {

        // ---------------------------------------

        initialize: function ($super, gridHandler) {
            $super(gridHandler);

            this.messageObj = Object.create(MessageObj);
        },

        // ---------------------------------------

        sendPartsResponses: [],
        errorMessages: [],
        errorsSummaryContainerId: 'listing_container_errors_summary',

        // ---------------------------------------

        setProgressBar: function (progressBarId) {
            this.progressBarObj = new ProgressBar(progressBarId);
        },

        setGridWrapper: function (wrapperId) {
            this.gridWrapperObj = new AreaWrapper(wrapperId);
        },

        setErrorsSummaryContainer: function (containerId) {
            this.errorsSummaryContainerId = containerId;
        },

        setActionMessagesContainer: function (containerId) {
            this.messageObj.setContainer('#' + containerId);
        },

        // ---------------------------------------

        startActions: function (title, url, selectedProductsParts, requestParams) {
            const self = this;

            if (typeof requestParams == 'undefined') {
                requestParams = {};
            }

            if (typeof requestParams['is_realtime'] == 'undefined') {
                requestParams['is_realtime'] = (this.gridHandler.getSelectedProductsArray().length <= 10);
            }

            self.messageObj.clear();

            $(self.errorsSummaryContainerId).hide();

            self.progressBarObj.reset();
            self.progressBarObj.show(title);
            self.gridWrapperObj.lock();

            self.sendPartsOfProducts(selectedProductsParts, selectedProductsParts.length, url, requestParams);

            $$('.loading-mask').invoke('setStyle', {visibility: 'hidden'});
        },

        sendPartsOfProducts: function (parts, totalPartsCount, url, requestParams) {
            const self = this;

            if (parts.length == totalPartsCount) {
                self.sendPartsResponses = new Array();
            }

            self.errorMessages = new Array();

            if (parts.length == 0) {

                self.progressBarObj.setPercents(100, 0);
                self.progressBarObj.setStatus(Temu.translator.translate('task_completed_message'));

                let combineResult = 'success';
                for (let i = 0; i < self.sendPartsResponses.length; i++) {
                    if (self.sendPartsResponses[i].messages && self.sendPartsResponses[i].messages.length > 0) {
                        self.errorMessages = self.sendPartsResponses[i].messages;
                    }

                    if (self.sendPartsResponses[i].result != 'success' && self.sendPartsResponses[i].result != 'warning') {
                        combineResult = 'error';
                        break;
                    }
                    if (self.sendPartsResponses[i].result == 'warning') {
                        combineResult = 'warning';
                    }
                }

                if (combineResult == 'error') {

                    if (self.errorMessages.length > 0) {
                        for (let i = 0; i < self.errorMessages.length; i++) {
                            self.messageObj.addError(self.errorMessages[i]);
                        }
                    } else {
                        let message = Temu.translator.translate('task_completed_error_message');
                        message = message.replace('%task_title%', self.progressBarObj.getTitle());
                        message = message.replace('%url%', Temu.url.get('logViewUrl'));

                        self.messageObj.addError(message);
                    }

                    let actionIds = '';
                    for (let i = 0; i < self.sendPartsResponses.length; i++) {
                        if (actionIds != '') {
                            actionIds += ',';
                        }
                        actionIds += self.sendPartsResponses[i].action_id;
                    }

                    new Ajax.Request(Temu.url.get('getErrorsSummary') + 'action_ids/' + actionIds + '/', {
                        method: 'get',
                        onSuccess: function (transportSummary) {
                            $(self.errorsSummaryContainerId).innerHTML = transportSummary.responseText;
                            $(self.errorsSummaryContainerId).show();
                        }
                    });

                } else if (combineResult == 'warning') {
                    let message = Temu.translator.translate('task_completed_warning_message');
                    message = message.replace('%task_title%', self.progressBarObj.getTitle());
                    message = message.replace('%url%', Temu.url.get('logViewUrl'));

                    self.messageObj.addWarning(message);
                } else {
                    let message = Temu.translator.translate('task_completed_success_message');
                    message = message.replace('%task_title%', self.progressBarObj.getTitle());

                    self.messageObj.addSuccess(message);
                }

                self.progressBarObj.hide();
                self.progressBarObj.reset();
                self.gridWrapperObj.unlock();
                $$('.loading-mask').invoke('setStyle', {visibility: 'visible'});

                self.sendPartsResponses = new Array();
                self.errorMessages = new Array();

                self.gridHandler.unselectAllAndReload();

                return;
            }

            let part = parts.splice(0, 1);
            part = part[0];
            let partString = implode(',', part);

            let partExecuteString = '';

            if (part.length <= 2) {

                for (let i = 0; i < part.length; i++) {

                    if (i != 0) {
                        partExecuteString += ', ';
                    }

                    let temp = self.gridHandler.getProductNameByRowId(part[i]);

                    if (temp != '') {
                        if (temp.length > 75) {
                            temp = temp.substr(0, 75) + '...';
                        }
                        partExecuteString += '"' + temp + '"';
                    } else {
                        partExecuteString = part.length;
                        break;
                    }
                }

            } else {
                partExecuteString = part.length;
            }

            partExecuteString += '';

            self.progressBarObj.setStatus(
                    str_replace(
                            '%product_title%',
                            partExecuteString,
                            Temu.translator.translate('sending_data_message')
                    )
            );

            if (typeof requestParams == 'undefined') {
                requestParams = {}
            }

            requestParams['selected_products'] = partString;

            new Ajax.Request(url + 'id/' + self.gridHandler.listingId, {
                method: 'post',
                parameters: requestParams,
                onSuccess: function (transport) {

                    if (!transport.responseText.isJSON()) {

                        if (transport.responseText != '') {
                            self.alert(transport.responseText);
                        }

                        self.progressBarObj.hide();
                        self.progressBarObj.reset();
                        self.gridWrapperObj.unlock();
                        $$('.loading-mask').invoke('setStyle', {visibility: 'visible'});

                        self.sendPartsResponses = new Array();

                        self.gridHandler.unselectAllAndReload();

                        return;
                    }

                    let response = transport.responseText.evalJSON(true);

                    if (response.error) {
                        self.progressBarObj.hide();
                        self.progressBarObj.reset();
                        self.gridWrapperObj.unlock();
                        $$('.loading-mask').invoke('setStyle', {visibility: 'visible'});

                        self.sendPartsResponses = new Array();

                        self.alert(response.message);

                        return;
                    }

                    self.sendPartsResponses[self.sendPartsResponses.length] = response;

                    let percents = (100 / totalPartsCount) * (totalPartsCount - parts.length);

                    if (percents <= 0) {
                        self.progressBarObj.setPercents(0, 0);
                    } else if (percents >= 100) {
                        self.progressBarObj.setPercents(100, 0);
                    } else {
                        self.progressBarObj.setPercents(percents, 1);
                    }

                    setTimeout(function () {
                        self.sendPartsOfProducts(parts, totalPartsCount, url);
                    }, 500);
                }
            });

            return;
        },

        // ---------------------------------------

        listAction: function () {
            let selectedProductsParts = this.gridHandler.getSelectedItemsParts();
            if (selectedProductsParts.length == 0) {
                return;
            }

            this.startActions(
                    Temu.translator.translate('listing_selected_items_message'),
                    Temu.url.get('runListProducts'),
                    selectedProductsParts
            );
        },

        relistAction: function () {
            let selectedProductsParts = this.gridHandler.getSelectedItemsParts();
            if (selectedProductsParts.length == 0) {
                return;
            }

            this.startActions(
                    Temu.translator.translate('relisting_selected_items_message'),
                    Temu.url.get('runRelistProducts'),
                    selectedProductsParts
            );
        },

        reviseAction: function () {
            let selectedProductsParts = this.gridHandler.getSelectedItemsParts();
            if (selectedProductsParts.length == 0) {
                return;
            }

            this.startActions(
                    Temu.translator.translate('revising_selected_items_message'),
                    Temu.url.get('runReviseProducts'),
                    selectedProductsParts
            );
        },

        stopAction: function () {
            let selectedProductsParts = this.gridHandler.getSelectedItemsParts(100);
            if (selectedProductsParts.length == 0) {
                return;
            }

            let requestParams = {'is_realtime': (this.gridHandler.getSelectedProductsArray().length <= 10)};

            this.startActions(
                    Temu.translator.translate('stopping_selected_items_message'),
                    Temu.url.get('runStopProducts'),
                    selectedProductsParts,
                    requestParams
            );
        },

        stopAndRemoveAction: function () {
            let selectedProductsParts = this.gridHandler.getSelectedItemsParts(100);
            if (selectedProductsParts.length == 0) {
                return;
            }

            let requestParams = {'is_realtime': (this.gridHandler.getSelectedProductsArray().length <= 10)};

            this.startActions(
                    Temu.translator.translate('stopping_and_removing_selected_items_message'),
                    Temu.url.get('runStopAndRemoveProducts'),
                    selectedProductsParts,
                    requestParams
            );
        },

        previewItemsAction: function () {
            let orderedSelectedProductsArray = this.gridHandler.getOrderedSelectedProductsArray();
            if (orderedSelectedProductsArray.length == 0) {
                return;
            }

            this.openWindow(
                    Temu.url.get('previewItems') + 'productIds/' + implode(',', orderedSelectedProductsArray)
                    + '/currentProductId/' + orderedSelectedProductsArray[0]
            );
        }

        // ---------------------------------------
    });
});
