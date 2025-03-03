define([
    'Temu/Product/Unmanaged/Move/RetrieveSelected',
    'Temu/Product/Unmanaged/Move/PrepareProducts',
    'Temu/Product/Unmanaged/Move/Processor',
], (RetrieveSelected, PrepareProducts, MoveProcess) => {
    'use strict';

    return {
        startMoveForProduct: (id, urlPrepareMove, urlGrid, urlListingCreate, accountId) => {
            PrepareProducts.prepareProducts(
                    urlPrepareMove,
                    [id],
                    accountId,
                    function () {
                        MoveProcess.openMoveToListingGrid(
                                urlGrid,
                                urlListingCreate,
                                accountId
                        );
                    }
            );
        },

        startMoveForProducts: (massActionData, urlPrepareMove, urlGrid, urlGetSelectedProducts, urlListingCreate, accountId) => {
            RetrieveSelected.getSelectedProductIds(
                    massActionData,
                    urlGetSelectedProducts,
                    accountId,
                    function (selectedProductIds) {
                        PrepareProducts.prepareProducts(
                                urlPrepareMove,
                                selectedProductIds,
                                accountId,
                                function (siteId) {
                                    MoveProcess.openMoveToListingGrid(
                                            urlGrid,
                                            urlListingCreate,
                                            accountId,
                                            siteId
                                    );
                                }
                        );
                    }
            );
        }
    };
});
