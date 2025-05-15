define([
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'Temu/Common',
    'mage/adminhtml/form',
], function(modal, $t) {

    window.TemuAccount = Class.create(Common, {

        // ---------------------------------------

        initialize: function() {
            jQuery.validator.addMethod('Temu-account-customer-id', function(value) {

                let checkResult = false;

                if ($('magento_orders_customer_id_container').getStyle('display') == 'none') {
                    return true;
                }

                new Ajax.Request(Temu.url.get('general/checkCustomerId'), {
                    method: 'post',
                    asynchronous: false,
                    parameters: {
                        customer_id: value,
                        id: Temu.formData.id,
                    },
                    onSuccess: function(transport) {
                        checkResult = transport.responseText.evalJSON()['ok'];
                    },
                });

                return checkResult;
            }, $t('No Customer entry is found for specified ID.'));

            jQuery.validator.addMethod(
                    'Temu-require-select-attribute',
                    function(value, el) {
                        if ($('other_listings_mapping_mode').value == 0) {
                            return true;
                        }

                        let isAttributeSelected = false;

                        $$('.attribute-mode-select').each(function(obj) {
                            if (obj.value != 0) {
                                isAttributeSelected = true;
                            }
                        });

                        return isAttributeSelected;
                    },
                    $t('If Yes is chosen, you must select at least one Attribute for Product Linking.'),
            );
        },

        initObservers: function() {

            if ($('temuAccountEditTabs_listingOther')) {

                $('other_listings_synchronization').
                        observe('change', this.other_listings_synchronization_change).
                        simulate('change');
                $('other_listings_mapping_mode').
                        observe('change', this.other_listings_mapping_mode_change).
                        simulate('change');
                $('mapping_sku_mode').observe('change', this.mapping_sku_mode_change).simulate('change');
                $('mapping_title_mode').observe('change', this.mapping_title_mode_change).simulate('change');
            }

            if ($('temuAccountEditTabs_order')) {

                $('magento_orders_listings_mode').
                        observe('change', this.magentoOrdersListingsModeChange).
                        simulate('change');
                $('magento_orders_listings_store_mode').
                        observe('change', this.magentoOrdersListingsStoreModeChange).
                        simulate('change');

                $('magento_orders_listings_other_mode').
                        observe('change', this.magentoOrdersListingsOtherModeChange).
                        simulate('change');

                $('magento_orders_number_source').observe('change', this.magentoOrdersNumberChange);
                $('magento_orders_number_prefix_prefix').observe('keyup', this.magentoOrdersNumberChange);

                TemuAccountObj.renderOrderNumberExample();

                $('magento_orders_customer_mode').
                        observe('change', this.magentoOrdersCustomerModeChange).
                        simulate('change');

                $('magento_orders_status_mapping_mode').observe('change', this.magentoOrdersStatusMappingModeChange);

                $('order_number_example-note').previous().remove();
            }
        },

        get_token: function (getTokenUrl) {
            this.submitForm(getTokenUrl);
        },

        // ---------------------------------------

        saveAndClose: function() {
            const self = this;
            const url = typeof Temu.url.urls.formSubmit == 'undefined'
                    ? Temu.url.formSubmit + 'back/' + Base64.encode('list') + '/'
                    : Temu.url.get('formSubmit', {'back': Base64.encode('list')});

            if (!this.isValidForm()) {
                return;
            }

            new Ajax.Request(url, {
                method: 'post',
                parameters: Form.serialize($('edit_form')),
                onSuccess: function(transport) {
                    transport = transport.responseText.evalJSON();

                    if (transport.success) {
                        window.close();
                    } else {
                        self.alert(transport.message);
                    }
                },
            });
        },

        // ---------------------------------------

        deleteClick: function(url, deleteMessage) {
            this.confirm({
                content: deleteMessage,
                actions: {
                    confirm: function() {
                        setLocation(url);

                    },
                    cancel: function() {
                        return false;
                    },
                },
            });
        },

        // ---------------------------------------

        magentoOrdersListingsModeChange: function() {
            const self = TemuAccountObj;

            if ($('magento_orders_listings_mode').value == 1) {
                $('magento_orders_listings_store_mode_container').show();
            } else {
                $('magento_orders_listings_store_mode_container').hide();
                $('magento_orders_listings_store_mode').value = Temu.php.constant(
                        'Account\\Settings\\Order::LISTINGS_STORE_MODE_DEFAULT');
            }

            self.magentoOrdersListingsStoreModeChange();
            self.changeVisibilityForOrdersModesRelatedBlocks();
        },

        magentoOrdersStatusMappingModeChange: function() {
            // Reset dropdown selected values to default
            $('magento_orders_status_mapping_processing').value = Temu.php.constant('Account\\Settings\\Order::ORDERS_STATUS_MAPPING_PROCESSING');
            $('magento_orders_status_mapping_shipped').value = Temu.php.constant('Account\\Settings\\Order::ORDERS_STATUS_MAPPING_SHIPPED');

            var disabled = $('magento_orders_status_mapping_mode').value == Temu.php.constant('Account\\Settings\\Order::ORDERS_STATUS_MAPPING_MODE_DEFAULT');
            $('magento_orders_status_mapping_processing').disabled = disabled;
            $('magento_orders_status_mapping_shipped').disabled = disabled;
        },

        magentoOrdersListingsStoreModeChange: function() {
            if ($('magento_orders_listings_store_mode').value ==
                    Temu.php.constant('Account\\Settings\\Order::LISTINGS_STORE_MODE_CUSTOM')) {
                $('magento_orders_listings_store_id_container').show();
            } else {
                $('magento_orders_listings_store_id_container').hide();
                $('magento_orders_listings_store_id').value = '';
            }
        },

        magentoOrdersListingsOtherModeChange: function() {
            const self = TemuAccountObj;

            if ($('magento_orders_listings_other_mode').value == 1) {
                $('magento_orders_listings_other_store_id_container').show();
            } else {
                $('magento_orders_listings_other_store_id_container').hide();
                $('magento_orders_listings_other_store_id').value = '';
            }

            self.changeVisibilityForOrdersModesRelatedBlocks();
        },

        magentoOrdersNumberChange: function() {
            const self = TemuAccountObj;
            self.renderOrderNumberExample();
        },

        renderOrderNumberExample: function() {
            let orderNumber = '123456789';
            if ($('magento_orders_number_source').value ==
                    Temu.php.constant('Account\\Settings\\Order::NUMBER_SOURCE_CHANNEL')) {
                orderNumber = '123412341234123100';
            }

            orderNumber = $('magento_orders_number_prefix_prefix').value + orderNumber;

            $('order_number_example_container').update(orderNumber);
        },

        magentoOrdersCustomerModeChange: function() {
            let customerMode = $('magento_orders_customer_mode').value;

            if (customerMode == Temu.php.constant('Account\\Settings\\Order::CUSTOMER_MODE_PREDEFINED')) {
                $('magento_orders_customer_id_container').show();
                $('magento_orders_customer_id').addClassName('Temu-account-product-id');
            } else {  // Temu.php.constant('Account\Settings\Order::ORDERS_CUSTOMER_MODE_GUEST') || Temu.php.constant('Account\Settings\Order::CUSTOMER_MODE_NEW')
                $('magento_orders_customer_id_container').hide();
                $('magento_orders_customer_id').value = '';
                $('magento_orders_customer_id').removeClassName('Temu-account-product-id');
            }

            let action = (customerMode == Temu.php.constant('Account\\Settings\\Order::CUSTOMER_MODE_NEW'))
                    ? 'show'
                    : 'hide';
            $('magento_orders_customer_new_website_id_container')[action]();
            $('magento_orders_customer_new_group_id_container')[action]();
            $('magento_orders_customer_new_notifications_container')[action]();

            if (action == 'hide') {
                $('magento_orders_customer_new_website_id').value = '';
                $('magento_orders_customer_new_group_id').value = '';
                $('magento_orders_customer_new_notifications').value = '';
            }
        },

        changeVisibilityForOrdersModesRelatedBlocks: function() {
            const self = TemuAccountObj;

            if ($('magento_orders_listings_mode').value == 0 && $('magento_orders_listings_other_mode').value == 0) {

                $('magento_block_temu_accounts_magento_orders_number-wrapper').hide();
                $('magento_orders_number_source').value = Temu.php.constant(
                        'Account\\Settings\\Order::NUMBER_SOURCE_MAGENTO');

                $('magento_block_temu_accounts_magento_orders_customer-wrapper').hide();
                $('magento_orders_customer_mode').value = Temu.php.constant(
                        'Account\\Settings\\Order::CUSTOMER_MODE_GUEST');
                self.magentoOrdersCustomerModeChange();

                $('magento_block_temu_accounts_magento_orders_rules-wrapper').hide();
                $('magento_orders_qty_reservation_days').value = 1;

                $('magento_block_temu_accounts_magento_orders_tax-wrapper').hide();
                $('magento_orders_tax_mode').value = Temu.php.constant('Account\\Settings\\Order::TAX_MODE_MIXED');

            } else {
                $('magento_block_temu_accounts_magento_orders_number-wrapper').show();
                $('magento_block_temu_accounts_magento_orders_customer-wrapper').show();
                $('magento_block_temu_accounts_magento_orders_rules-wrapper').show();
                $('magento_block_temu_accounts_magento_orders_tax-wrapper').show();
            }
        },

        // ---------------------------------------

        other_listings_synchronization_change: function() {
            const relatedStoreViews = $('magento_block_accounts_other_listings_related_store_views-wrapper');

            if (this.value == 1) {
                $('other_listings_mapping_mode_tr').show();
                $('other_listings_mapping_mode').simulate('change');
                if (relatedStoreViews) {
                    relatedStoreViews.show();
                }
            } else {
                $('other_listings_mapping_mode').value = 0;
                $('other_listings_mapping_mode').simulate('change');
                $('other_listings_mapping_mode_tr').hide();
                if (relatedStoreViews) {
                    relatedStoreViews.hide();
                }
            }
        },

        other_listings_mapping_mode_change: function() {
            if (this.value == 1) {
                $('magento_block_temu_accounts_other_listings_product_mapping-wrapper').show();
            } else {
                $('magento_block_temu_accounts_other_listings_product_mapping-wrapper').hide();

                $('mapping_sku_mode').value = Temu.php.constant(
                        'Account\\Settings\\UnmanagedListings::MAPPING_SKU_MODE_NONE');
                $('mapping_title_mode').value = Temu.php.constant(
                        'Account\\Settings\\UnmanagedListings::MAPPING_TITLE_MODE_NONE');
            }

            $('mapping_sku_mode').simulate('change');
            $('mapping_title_mode').simulate('change');
        },

        synchronization_mapped_change: function() {
            if (this.value == 0) {
                $('settings_button').hide();
            } else {
                $('settings_button').show();
            }
        },

        mapping_sku_mode_change: function() {
            const self = TemuAccountObj,
                    attributeEl = $('mapping_sku_attribute');

            $('mapping_sku_priority').hide();
            if (this.value != Temu.php.constant('Account\\Settings\\UnmanagedListings::MAPPING_SKU_MODE_NONE')) {
                $('mapping_sku_priority').show();
            }

            attributeEl.value = '';
            if (this.value ==
                    Temu.php.constant('Account\\Settings\\UnmanagedListings::MAPPING_SKU_MODE_CUSTOM_ATTRIBUTE')) {
                self.updateHiddenValue(this, attributeEl);
            }
        },

        mapping_title_mode_change: function() {
            const self = TemuAccountObj,
                    attributeEl = $('mapping_title_attribute');

            $('mapping_title_priority').hide();
            if (this.value != Temu.php.constant('Account\\Settings\\UnmanagedListings::MAPPING_TITLE_MODE_NONE')) {
                $('mapping_title_priority').show();
            }

            attributeEl.value = '';
            if (this.value ==
                    Temu.php.constant('Account\\Settings\\UnmanagedListings::MAPPING_TITLE_MODE_CUSTOM_ATTRIBUTE')) {
                self.updateHiddenValue(this, attributeEl);
            }
        },

        mapping_opc_mode_change: function() {
            const self = TemuAccountObj,
                    attributeEl = $('mapping_opc_attribute');

            $('mapping_opc_priority').hide();
            if (this.value != Temu.php.constant('Account\\Settings\\UnmanagedListings::MAPPING_OPC_MODE_NONE')) {
                $('mapping_opc_priority').show();
            }

            attributeEl.value = '';
            if (this.value ==
                    Temu.php.constant('Account\\Settings\\UnmanagedListings::MAPPING_OPC_MODE_CUSTOM_ATTRIBUTE')) {
                self.updateHiddenValue(this, attributeEl);
            }
        },
    });
});
