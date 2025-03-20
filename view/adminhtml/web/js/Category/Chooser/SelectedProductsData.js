define([
    'Temu/Common'
], function () {

    window.SelectedProductsData = Class.create(Common, {

        // ---------------------------------------

        wizardId: null,
        productId: null,
        region: null,

        // ---------------------------------------

        getWizardId: function () {
            return this.wizardId;
        },

        setWizardId: function (id) {
            this.wizardId = id;
        },

        // ---------------------------------------

        getRegion: function () {
            return this.region;
        },

        setRegion: function (region) {
            this.region = region;
        },

        // ---------------------------------------

        getProductId: function () {
            return this.productId;
        },

        setProductId: function (id) {
            this.productId = id;
        }
    });
});
