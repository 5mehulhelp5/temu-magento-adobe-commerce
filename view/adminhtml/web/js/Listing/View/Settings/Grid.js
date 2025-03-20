define([
    'jquery',
    'Temu/Listing/View/Grid',
    'Temu/Listing/Wizard/Category',
    'Temu/Listing/MovingFromListing',
    'Magento_Ui/js/modal/modal'
], function (jQuery) {

    window.TemuListingViewSettingsGrid = Class.create(ListingViewGrid, {

        // ---------------------------------------

        accountId: null,
        region: null,

        // ---------------------------------------

        initialize: function ($super, gridId, listingId, accountId, region) {
            this.accountId = accountId;
            this.region = region;

            $super(gridId, listingId);
        },

        // ---------------------------------------

        prepareActions: function ($super) {
            $super();

            this.movingHandler = new MovingFromListing(this);
            this.categoryHandler = new TemuListingCategory(this);

            this.actions = Object.extend(this.actions, {
                movingAction: this.movingHandler.run.bind(this.movingHandler),
                editCategorySettingsAction: this.categoryHandler.editCategorySettings.bind(this.categoryHandler),
            });
        },

        // ---------------------------------------

        tryToMove: function (listingId) {
            this.movingHandler.submit(listingId, this.onSuccess)
        },

        onSuccess: function () {
            this.unselectAllAndReload();
        },

        // ---------------------------------------

        confirm: function (config) {
            if (config.actions && config.actions.confirm) {
                config.actions.confirm();
            }
        },

        // ---------------------------------------
    });
});
