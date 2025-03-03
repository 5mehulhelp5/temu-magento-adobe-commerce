define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Temu/Common'
], function (jQuery, modal) {

    window.TemuListingTemplateSwitcher = Class.create(Common, {

        // ---------------------------------------

        storeId: null,
        listingProductIds: '',

        // ---------------------------------------

        initialize: function () {
            jQuery.validator.addMethod('Temu-validate-template-title', function (value, el) {

                var mode = base64_decode(value).evalJSON().mode;

                return mode !== null;
            }, Temu.translator.translate('This is a required field.'));

            jQuery.validator.addMethod('Temu-validate-template-title', function (value, el) {

                var templateNick = el.name.substr(0, el.name.indexOf('['));

                return TemuListingTemplateSwitcherObj.isTemplateTitleUnique(templateNick, value);
            }, Temu.translator.translate('Policy with the same Title already exists.'));
        },

        // ---------------------------------------

        getSwitcherNickByElementId: function (id) {
            return id.replace('template_', '');
        },

        getSwitcherElementId: function (templateNick) {
            return 'template_' + templateNick;
        },

        getSwitcher: function (templateNick) {
            return $(this.getSwitcherElementId(templateNick));
        },

        getSwitcherValueId: function (templateNick) {
            var switcher = this.getSwitcher(templateNick);
            var switcherValue = base64_decode(switcher.value).evalJSON();

            return switcherValue.id;
        },

        getSwitcherValueMode: function (templateNick) {
            var switcher = this.getSwitcher(templateNick);
            var switcherValue = base64_decode(switcher.value).evalJSON();

            return switcherValue.mode;
        },

        isSwitcherValueModeEmpty: function (templateNick) {
            return this.getSwitcherValueMode(templateNick) === null;
        },

        isSwitcherValueModeParent: function (templateNick) {
            return this.getSwitcherValueMode(templateNick) == Temu.php.constant('M2E_Temu_Model_Temu_Template_Manager::MODE_PARENT');
        },

        isSwitcherValueModeCustom: function (templateNick) {
            return this.getSwitcherValueMode(templateNick) == Temu.php.constant('M2E_Temu_Model_Temu_Template_Manager::MODE_CUSTOM');
        },

        isSwitcherValueModeTemplate: function (templateNick) {
            return this.getSwitcherValueMode(templateNick) == Temu.php.constant('M2E_Temu_Model_Temu_Template_Manager::MODE_TEMPLATE');
        },

        isExistSynchronizationTab: function () {
            return typeof TemuTemplateSynchronizationHandlerObj != 'undefined';
        },

        isNeededSaveWatermarkImage: function (ajaxResponse) {
            var isDescriptionTemplate = false;

            ajaxResponse.each(function (template) {
                if (template.nick == Temu.php.constant('M2E_Temu_Model_Temu_Template_Manager::TEMPLATE_DESCRIPTION')) {
                    isDescriptionTemplate = true;
                }
            });

            return isDescriptionTemplate && $('watermark_image').value != '';
        },

        getTemplateDataContainer: function (templateNick) {
            return $('template_' + templateNick + '_data_container');
        },

        // ---------------------------------------

        change: function () {
            var templateNick = TemuListingTemplateSwitcherObj.getSwitcherNickByElementId(this.id);
            var templateMode = TemuListingTemplateSwitcherObj.getSwitcherValueMode(templateNick);

            TemuListingTemplateSwitcherObj.clearMessages(templateNick);

            switch (templateMode) {
                case Temu.php.constant('M2E_Temu_Model_Temu_Template_Manager::MODE_PARENT'):
                    TemuListingTemplateSwitcherObj.clearContent(templateNick);
                    break;

                case Temu.php.constant('M2E_Temu_Model_Temu_Template_Manager::MODE_CUSTOM'):
                    TemuListingTemplateSwitcherObj.reloadContent(templateNick);
                    break;

                case Temu.php.constant('M2E_Temu_Model_Temu_Template_Manager::MODE_TEMPLATE'):
                    TemuListingTemplateSwitcherObj.clearContent(templateNick);
                    TemuListingTemplateSwitcherObj.checkMessages(templateNick);
                    break;
            }

            TemuListingTemplateSwitcherObj.hideEmptyOption(TemuListingTemplateSwitcherObj.getSwitcher(templateNick));

            if (!TemuListingTemplateSwitcherObj.isSwitcherValueModeCustom(templateNick)) {
                TemuListingTemplateSwitcherObj.updateButtonsVisibility(templateNick);
                TemuListingTemplateSwitcherObj.updateEditVisibility(templateNick);
                TemuListingTemplateSwitcherObj.updateTemplateLabelVisibility(templateNick);
            }
        },

        // ---------------------------------------

        clearMessages: function (templateNick) {
            $('template_switcher_' + templateNick + '_messages').innerHTML = '';
        },

        checkMessages: function (templateNick) {

            var id = this.getSwitcherValueId(templateNick),
                    nick = templateNick,
                    data = Form.serialize(this.getTemplateDataContainer(templateNick).id)
                            + '&listing_product_ids=' + this.listingProductIds,
                    storeId = this.storeId,
                    container = 'template_switcher_' + templateNick + '_messages',
                    callback = function () {
                        var refresh = $(container).down('a.refresh-messages');
                        if (refresh) {
                            refresh.observe('click', function () {
                                this.checkMessages(templateNick);
                            }.bind(this))
                        }
                    }.bind(this);

            TemplateManagerObj.checkMessages(
                    id,
                    nick,
                    data,
                    storeId,
                    container,
                    callback
            );
        },

        // ---------------------------------------

        updateEditVisibility: function (templateNick) {
            var tdEdit = $('template_' + templateNick + '_edit');

            if (!tdEdit) {
                return;
            }

            if (this.isSwitcherValueModeTemplate(templateNick)) {
                tdEdit.show();
            } else {
                tdEdit.hide();
            }
        },

        updateButtonsVisibility: function (templateNick) {
            var divButtonsContainer = $('template_' + templateNick + '_buttons_container');

            if (!divButtonsContainer) {
                return;
            }

            if (this.isSwitcherValueModeCustom(templateNick)) {
                divButtonsContainer.show();
            } else {
                divButtonsContainer.hide();
            }
        },

        updateTemplateLabelVisibility: function (templateNick) {
            var labelContainer = $('template_' + templateNick + '_nick_label');
            var templateLabel = labelContainer.down('span.template');

            labelContainer.hide();
            templateLabel && templateLabel.hide();

            if (this.isSwitcherValueModeTemplate(templateNick)) {
                labelContainer.show();
                templateLabel && templateLabel.show();
            }

            if (this.isSwitcherValueModeEmpty(templateNick)) {
                labelContainer.hide();
            }
        },

        // ---------------------------------------

        isTemplateTitleUnique: function (templateNick, templateTitle) {
            var unique = true;

            new Ajax.Request(Temu.url.get('policy/isTitleUnique'), {
                method: 'get',
                asynchronous: false,
                parameters: {
                    nick: templateNick,
                    title: templateTitle
                },
                onSuccess: function (transport) {
                    unique = transport.responseText.evalJSON()['unique'];
                }
            });

            return unique;
        },

        // ---------------------------------------

        customSaveAsTemplate: function (templateNick) {
            new Ajax.Request(Temu.url.get('policy/newTemplateHtml'), {
                method: 'GET',
                parameters: {
                    nick: templateNick
                },
                onSuccess: (function (transport) {
                    if ($('new_template_form_' + templateNick)) {
                        $('new_template_form_' + templateNick).remove();
                    }

                    $('html-body').insert({bottom: transport.responseText});

                    var form = jQuery('#new_template_form_' + templateNick);

                    form.form().validation();

                    modal({
                        title: Temu.translator.translate('Save as New Policy'),
                        type: 'popup',
                        buttons: [{
                            text: Temu.translator.translate('Cancel'),
                            class: 'action-secondary action-dismiss',
                            click: function () {
                                form.modal('closeModal');
                                $('new_template_form_' + templateNick).remove()
                            }
                        }, {
                            text: Temu.translator.translate('Save'),
                            class: 'action-primary action-accept',
                            click: function () {
                                if (!form.form().valid()) {
                                    return false;
                                }

                                $$('input[name="' + templateNick + '[id]"]')[0].value = '';
                                $$('input[name="' + templateNick + '[is_custom_template]"]')[0].value = 0;
                                $$('input[name="' + templateNick + '[title]"]')[0].value = $('template_title_' + templateNick).value;

                                $('edit_form').request({
                                    method: 'post',
                                    asynchronous: true,
                                    parameters: {
                                        nick: templateNick
                                    },
                                    onSuccess: function (transport) {

                                        var response = transport.responseText.evalJSON();

                                        response.each(function (template) {
                                            TemuListingTemplateSwitcherObj.addToSwitcher(template.nick, template.id, template.title);
                                            TemuListingTemplateSwitcherObj.clearContent(template.nick);
                                            TemuListingTemplateSwitcherObj.updateButtonsVisibility(template.nick);
                                            TemuListingTemplateSwitcherObj.updateEditVisibility(template.nick);
                                            TemuListingTemplateSwitcherObj.updateTemplateLabelVisibility(template.nick);
                                            TemuListingTemplateSwitcherObj.checkMessages(template.nick);
                                        });
                                    }.bind(this)
                                });

                                form.modal('closeModal');
                            }
                        }]
                    }, form);

                    form.modal('openModal');
                }).bind(this)
            });
        },

        afterCustomSaveAsTemplate: function (templateNick, templateId, templateTitle) {
            $$('input[name="' + templateNick + '[id]"]')[0].value = templateId;
            $$('input[name="' + templateNick + '[title]"]')[0].value = templateTitle;

            var switcher = TemuListingTemplateSwitcherObj.getSwitcher(templateNick);

            switcher.down('.template-switcher-custom-option').value = base64_encode(
                    Object.toJSON({
                        mode: Temu.php.constant('M2E_Temu_Model_Temu_Template_Manager::MODE_CUSTOM'),
                        nick: templateNick,
                        id: templateId
                    })
            );
        },

        // ---------------------------------------

        editTemplate: function (templateNick) {
            var templateId = this.getSwitcherValueId(templateNick);

            window.open(
                    Temu.url.get('policy/edit', {
                        id: templateId,
                        nick: templateNick,
                        close_on_save: 1
                    }), '_blank'
            );
        },

        // ---------------------------------------

        customizeTemplate: function (templateNick) {
            this.clearMessages(templateNick);
            this.reloadContent(templateNick, function () {
                $$('input[name="' + templateNick + '[id]"]')[0].value = TemuListingTemplateSwitcherObj.getSwitcherValueId(templateNick);
                $$('input[name="' + templateNick + '[is_custom_template]"]')[0].value = 1;
                $$('input[name="' + templateNick + '[title]"]')[0].value += ' [' + Temu.translator.translate('Customized') + ']';
            });
            this.getSwitcher(templateNick).selectedIndex = 0;
        },

        // ---------------------------------------

        clearContent: function (templateNick) {
            this.getTemplateDataContainer(templateNick).innerHTML = '';
            this.getTemplateDataContainer(templateNick).hide();
        },

        // ---------------------------------------

        reloadContent: function (templateNick, callback) {
            var id = this.getSwitcherValueId(templateNick);
            var self = this;

            new Ajax.Request(Temu.url.get('policy/getTemplateHtml'), {
                method: 'get',
                asynchronous: true,
                parameters: {
                    id: id,
                    nick: templateNick
                },
                onSuccess: function (transport) {

                    BlockNoticeObj.initializedBlocks = [];

                    self.getTemplateDataContainer(templateNick).replace(transport.responseText);
                    self.getTemplateDataContainer(templateNick).show();

                    self.updateButtonsVisibility(templateNick);
                    self.updateEditVisibility(templateNick);
                    self.updateTemplateLabelVisibility(templateNick);

                    if (typeof callback == 'function') {
                        callback();
                    }

                }
            });
        },

        // ---------------------------------------

        addToSwitcher: function (templateNick, templateId, templateTitle) {
            var switcher = this.getSwitcher(templateNick);
            var optionGroup = this.getTemplatesGroup(templateNick);

            var optionValue = {
                mode: Temu.php.constant('M2E_Temu_Model_Temu_Template_Manager::MODE_TEMPLATE'),
                nick: templateNick,
                id: templateId
            };
            var option = document.createElement('option');

            option.value = base64_encode(Object.toJSON(optionValue));
            option.innerHTML = templateTitle;

            $(optionGroup).appendChild(option);

            switcher.up('td').show();
            switcher.value = option.value;
        },

        getTemplatesGroup: function (templateNick) {
            var switcher = this.getSwitcher(templateNick);
            var optionGroup = switcher.down('optgroup.templates-group');

            if (typeof optionGroup != 'undefined') {
                return optionGroup;
            }

            optionGroup = document.createElement('optgroup');
            optionGroup.className = 'templates-group';
            optionGroup.label = Temu.translator.translate('Policies');

            switcher.appendChild(optionGroup);

            return optionGroup;
        }

        // ---------------------------------------
    });

});
