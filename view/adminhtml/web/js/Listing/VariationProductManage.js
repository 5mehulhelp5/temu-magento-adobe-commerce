define([
    'jquery',
    'Temu/Common'
], function ($, modal, messageObj) {
    window.TemuListingVariationProductManage = Class.create(Common, {
        openPopUp: function (productId, title) {
            const self = this;

            let requestParams = {
                product_id: productId
            };

            new Ajax.Request(Temu.url.get('variationProductManageOpenPopupUrl'), {
                method: 'post',
                parameters: requestParams,
                onSuccess: function (transport) {

                    const modalContainer = self.getModalContainer('modal_variation_product_manage')

                    window.variationProductManagePopup = $(modalContainer).modal({
                        title: title.escapeHTML(),
                        type: 'slide',
                        buttons: []
                    });
                    variationProductManagePopup.modal('openModal');

                    modalContainer.insert(transport.responseText);
                    modalContainer.innerHTML.evalScripts();

                    variationProductManagePopup.productId = productId;
                }
            });
        },

        getModalContainer: function (containerId)
        {
            let modalContainer = $('#' + containerId);
            if (modalContainer.length) {
                modalContainer.remove()
            }

            modalContainer = new Element('div', { id: containerId });

            return modalContainer
        }
    });
});
