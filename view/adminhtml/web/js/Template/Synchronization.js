define([
    'Temu/Common',
    'Magento_Ui/js/modal/confirm'
], function () {

    window.TemuTemplateSynchronization = Class.create(Common, {
        // ---------------------------------------

        initialize: function () {
            var self = this;

            jQuery.validator.addMethod('Temu-validate-qty', function (value, el) {

                if (self.isElementHiddenFromPage(el)) {
                    return true;
                }

                if (value.match(/[^\d]+/g) || value <= 0) {
                    return false;
                }

                return true;
            }, Temu.translator.translate('Wrong value. Only integer numbers.'));

            jQuery.validator.addMethod('Temu-validate-stop-relist-conditions-product-status', function (value, el) {

                if (TemuTemplateSynchronizationObj.isRelistModeDisabled()) {
                    return true;
                }

                if (TemuTemplateSynchronizationObj.isStopModeDisabled()) {
                    return true;
                }

                if ($('stop_status_disabled').value == 1 && $('relist_status_enabled').value == 0) {
                    return false;
                }

                return true;
            }, Temu.translator.translate('Inconsistent Settings in Relist and Stop Rules.'));

            jQuery.validator.addMethod('Temu-validate-stop-relist-conditions-stock-availability', function (value, el) {

                if (TemuTemplateSynchronizationObj.isRelistModeDisabled()) {
                    return true;
                }

                if (TemuTemplateSynchronizationObj.isStopModeDisabled()) {
                    return true;
                }

                if ($('stop_out_off_stock').value == 1 && $('relist_is_in_stock').value == 0) {
                    return false;
                }

                return true;
            }, Temu.translator.translate('Inconsistent Settings in Relist and Stop Rules.'));

            jQuery.validator.addMethod('Temu-validate-stop-relist-conditions-item-qty', function (value, el) {

                if (TemuTemplateSynchronizationObj.isRelistModeDisabled()) {
                    return true;
                }

                if (TemuTemplateSynchronizationObj.isStopModeDisabled()) {
                    return true;
                }

                var stopMaxQty = 0,
                        relistMinQty = 0;

                switch (parseInt($('stop_qty_calculated').value)) {

                    case Temu.php.constant('M2E_Temu_Model_Policy_Synchronization::QTY_MODE_NONE'):
                        return true;
                        break;

                    case Temu.php.constant('M2E_Temu_Model_Policy_Synchronization::QTY_MODE_YES'):
                        stopMaxQty = parseInt($('stop_qty_calculated_value').value);
                        break;
                }

                switch (parseInt($('relist_qty_calculated').value)) {

                    case Temu.php.constant('M2E_Temu_Model_Policy_Synchronization::QTY_MODE_NONE'):
                        return false;
                        break;

                    case Temu.php.constant('M2E_Temu_Model_Policy_Synchronization::QTY_MODE_YES'):
                        relistMinQty = parseInt($('relist_qty_calculated_value').value);
                        break;
                }

                if (relistMinQty <= stopMaxQty) {
                    return false;
                }

                return true;
            }, Temu.translator.translate('Inconsistent Settings in Relist and Stop Rules.'));
            // ---------------------------------------
        },

        initObservers: function () {
            $$('#advanced_filter select.element-value-changer option').each(function (el) {
                if ((el.value == '??' && el.selected) || (el.value == '!??' && el.selected)) {
                    setTimeout(function () {
                        $(el.parentElement.parentElement.parentElement.nextElementSibling).hide();
                    }, 10);
                }
            });
            $$(
                    '#template_synchronization_list_advanced_rules',
                    '#template_synchronization_relist_advanced_rules',
                    '#template_synchronization_stop_advanced_rules',
            )
                    .invoke('observe', 'change', function (event) {
                        let target = event.target;
                        if (target.value == '??' || target.value == '!??') {
                            setTimeout(function () {
                                $(target.parentElement.parentElement.nextElementSibling).hide();
                            }, 10);
                        }
                    });

            //list
            $('list_mode')
                    .observe('change', TemuTemplateSynchronizationObj.listMode_change).simulate('change');

            $('list_qty_calculated')
                    .observe('change', TemuTemplateSynchronizationObj.listQtyChange)
                    .simulate('change');

            $('list_advanced_rules_mode')
                    .observe('change', TemuTemplateSynchronizationObj.listAdvancedRules_change)
                    .simulate('change');

            //relist
            $('relist_mode')
                    .observe('change', TemuTemplateSynchronizationObj.relistMode_change)
                    .simulate('change');

            $('relist_qty_calculated')
                    .observe('change', TemuTemplateSynchronizationObj.relistQtyChange)
                    .simulate('change');

            $('relist_advanced_rules_mode')
                    .observe('change', TemuTemplateSynchronizationObj.relistAdvancedRules_change)
                    .simulate('change');

            //revise
            $('revise_update_qty')
                    .observe('change', TemuTemplateSynchronizationObj.reviseQty_change)
                    .simulate('change');

            $('revise_update_qty_max_applied_value_mode')
                    .observe('change', TemuTemplateSynchronizationObj.reviseQtyMaxAppliedValueMode_change)
                    .simulate('change');

            //stop
            $('stop_mode').observe('change', TemuTemplateSynchronizationObj.stopMode_change)
                    .simulate('change');

            $('stop_qty_calculated')
                    .observe('change', TemuTemplateSynchronizationObj.stopQtyChange)
                    .simulate('change');

            $('stop_advanced_rules_mode')
                    .observe('change', TemuTemplateSynchronizationObj.stopAdvancedRules_change)
                    .simulate('change');
        },

        // ---------------------------------------

        isRelistModeDisabled: function () {
            return $('relist_mode').value == 0;
        },

        isStopModeDisabled: function () {
            return $('stop_mode').value == 0;
        },

        // ---------------------------------------

        listMode_change: function () {
            var rulesContainer = $('magento_block_template_synchronization_form_data_list_rules'),
                    advancedRulesContainer = $('magento_block_template_synchronization_list_advanced_filters');

            rulesContainer.hide();
            advancedRulesContainer.hide();

            if (this.value == 1) {
                rulesContainer.show();
                advancedRulesContainer.show();
            }
        },

        listQtyChange: function () {
            var valueContainer = $('list_qty_calculated_value');
            valueContainer.hide();

            if (this.value == Temu.php.constant('M2E_Temu_Model_Policy_Synchronization::QTY_MODE_YES')) {
                valueContainer.show();
            }
        },

        // ---------------------------------------

        reviseQty_change: function () {
            if (this.value == 1) {
                $('revise_update_qty_max_applied_value_mode_tr').show();
                $('revise_update_qty_max_applied_value_line_tr').show();
                $('revise_update_qty_max_applied_value_mode').simulate('change');
            } else {
                $('revise_update_qty_max_applied_value_mode_tr').hide();
                $('revise_update_qty_max_applied_value_line_tr').hide();
                $('revise_update_qty_max_applied_value_mode').value = 0;
            }
        },

        reviseQtyMaxAppliedValueMode_change: function (event) {
            var self = TemuTemplateSynchronizationObj;

            $('revise_update_qty_max_applied_value').hide();

            if (this.value == 1) {
                $('revise_update_qty_max_applied_value').show();
            } else if (!event.cancelable) {
                self.openReviseMaxAppliedQtyDisableConfirmationPopUp();
            }
        },

        openReviseMaxAppliedQtyDisableConfirmationPopUp: function () {
            var self = this;

            var element = jQuery('#revise_qty_max_applied_value_confirmation_popup_template').clone();

            element.confirm({
                title: Temu.translator.translate('Are you sure?'),
                actions: {
                    confirm: self.reviseQtyMaxAppliedValueDisableConfirm,
                    cancel: self.reviseQtyMaxAppliedValueDisableCancel
                },
                buttons: [{
                    text: Temu.translator.translate('Cancel'),
                    class: 'action-secondary action-dismiss',
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: Temu.translator.translate('Confirm'),
                    class: 'action-primary action-accept',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }]
            });
        },

        reviseQtyMaxAppliedValueDisableCancel: function () {
            $('revise_update_qty_max_applied_value_mode').selectedIndex = 1;
            $('revise_update_qty_max_applied_value_mode').simulate('change');
        },

        reviseQtyMaxAppliedValueDisableConfirm: function () {
            $('revise_update_qty_max_applied_value_mode').selectedIndex = 0;
            $('revise_update_qty_max_applied_value_mode').simulate('change');
        },

        // ---------------------------------------

        relistMode_change: function () {
            var rulesContainer = $('magento_block_template_synchronization_form_data_relist_rules'),
                    advancedRulesContainer = $('magento_block_template_synchronization_relist_advanced_filters'),
                    userLockContainer = $('relist_filter_user_lock_tr_container');

            userLockContainer.hide();
            rulesContainer.hide();
            advancedRulesContainer.hide();

            if (this.value == 1) {
                userLockContainer.show();
                rulesContainer.show();
                advancedRulesContainer.show();
            }
        },

        relistQtyChange: function (event) {
            TemuTemplateSynchronizationObj.qtyChange(event, this, 'relist');
        },

        // ---------------------------------------

        stopQtyChange: function (event) {
            TemuTemplateSynchronizationObj.qtyChange(event, this, 'stop');
        },

        // ---------------------------------------

        stopMode_change: function () {
            var rulesContainer = $('magento_block_template_synchronization_stop_rules'),
                    advancedRulesContainer = $('magento_block_template_synchronization_stop_advanced_filters');

            rulesContainer.hide();
            advancedRulesContainer.hide();

            if ($('stop_mode').value == 1) {
                rulesContainer.show();
                advancedRulesContainer.show();
            }
        },

        // ---------------------------------------

        listAdvancedRules_change: function () {
            var rulesContainer = $('list_advanced_rules_filters_container'),
                    warningContainer = $('list_advanced_rules_filters_warning');

            rulesContainer.hide();
            warningContainer.hide();

            if (this.value == 1) {
                rulesContainer.show();
                warningContainer.show();
            }
        },

        relistAdvancedRules_change: function () {
            var rulesContainer = $('relist_advanced_rules_filters_container'),
                    warningContainer = $('relist_advanced_rules_filters_warning');

            rulesContainer.hide();
            warningContainer.hide();

            if (this.value == 1) {
                rulesContainer.show();
                warningContainer.show();
            }
        },

        stopAdvancedRules_change: function () {
            var rulesContainer = $('stop_advanced_rules_filters_container'),
                    warningContainer = $('stop_advanced_rules_filters_warning');

            rulesContainer.hide();
            warningContainer.hide();

            if (this.value == 1) {
                rulesContainer.show();
                warningContainer.show();
            }
        },

        // ---------------------------------------

        qtyChange: function (event, element, action) {
            var qtyCalculatedValue = $(action + '_qty_calculated_value');

            qtyCalculatedValue.hide();

            if (element.value == Temu.php.constant('M2E_Temu_Model_Policy_Synchronization::QTY_MODE_YES')) {
                qtyCalculatedValue.show();
            } else if (!event.cancelable) {
                TemuTemplateSynchronizationObj.openQtyCalculatedConfirmationPopUp(element, action);
            }
        },

        openQtyCalculatedConfirmationPopUp: function (element, action) {
            var popupTemplate = jQuery('#' + action + '_qty_calculated_confirmation_popup_template').clone();

            popupTemplate.confirm({
                title: Temu.translator.translate('Are you sure?'),
                actions: {
                    confirm: function () {
                        element.selectedIndex = 0;
                        element.simulate('change');
                    },
                    cancel: function () {
                        element.selectedIndex = 1;
                        element.simulate('change');
                    },
                },
                buttons: [
                    {
                        text: Temu.translator.translate('Cancel'),
                        class: 'action-secondary action-dismiss',
                        click: function (event) {
                            this.closeModal(event);
                        }
                    },
                    {
                        text: Temu.translator.translate('Confirm'),
                        class: 'action-primary action-accept',
                        click: function (event) {
                            this.closeModal(event, true);
                        }
                    }
                ]
            });
        }

        // ---------------------------------------
    });
});
