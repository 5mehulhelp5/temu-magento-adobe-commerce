define([
    'Temu/Grid',
    'prototype'
], function () {

    window.TemuListingGrid = Class.create(Grid, {

        // ---------------------------------------

        backParam: base64_encode('*/temu_listing/index'),

        // ---------------------------------------

        prepareActions: function () {
            return false;
        },

        // ---------------------------------------

        addProductsSourceProductsAction: function (id) {
            setLocation(Temu.url.get('listing_product_add/index', {
                id: id,
                source: 'product',
                clear: true,
                back: this.backParam
            }));
        },

        // ---------------------------------------

        addProductsSourceCategoriesAction: function (id) {
            setLocation(Temu.url.get('listing_product_add/index', {
                id: id,
                source: 'category',
                clear: true,
                back: this.backParam
            }));
        }

        // ---------------------------------------
    });

});
