define([], function () {
    window.Attribute = Class.create({

        // ---------------------------------------

        attrData: '',

        availableAttributes: [],

        // ---------------------------------------

        setAvailableAttributes: function (attributes) {
            this.availableAttributes = attributes;
        },

        // ---------------------------------------

        initialize: function (selectId) {
        },

        // ---------------------------------------

        appendToText: function (ddId, targetId) {
            if ($(ddId).value == '') {
                return;
            }

            var suffix = '#' + $(ddId).value + '#';
            $(targetId).value = $(targetId).value + suffix;
        },

        // ---------------------------------------

        checkAttributesSelect: function (id, value) {
            if ($(id)) {
                if (typeof Temu.formData[id] != 'undefined') {
                    $(id).value = Temu.formData[id];
                }
                if (value) {
                    $(id).value = value;
                }
            }
        },

        renderAttributes: function (id, insertTo, value, width) {
            var style = width ? ' style="width: ' + width + 'px;"' : '';
            var txt = '<select class="select admin__control-select" name="' + id + '" id="' + id + '"' + style + '>\n';

            txt += this.attrData;
            txt += '</select>';

            $(insertTo).innerHTML = txt;
            this.checkAttributesSelect(id, value);
        },

        renderAttributesWithEmptyHiddenOption: function (id, insertTo, value, width) {
            var style = width ? ' style="width: ' + width + 'px;"' : '';
            var txt = '<select name="' + id + '" id="' + id + '" class="Temu-required-when-visible select admin__control-select"' + style + '>\n';

            txt += '<option style="display: none;"></option>\n';
            txt += this.attrData;
            txt += '</select>';

            $(insertTo).innerHTML = txt;
            this.checkAttributesSelect(id, value);
        },

        renderAttributesWithEmptyOption: function (id, insertTo, value, notRequiried) {
            var classes = 'Temu-custom-attribute-can-be-created select admin__control-select';

            if (!notRequiried) {
                classes += ' Temu-required-when-visible';
            }

            var txt = '<select name="' + id + '" id="' + id + '" class="' + classes + '" allowed_attribute_types="text,price,select">\n';

            txt += '<option class="empty"></option>\n';
            txt += this.attrData;
            txt += '</select>';

            if ($(insertTo + '_note') != null && $$('#' + insertTo + '_note').length != 0) {
                $(insertTo).innerHTML = txt + $(insertTo + '_note').innerHTML;
            } else {
                $(insertTo).innerHTML = txt;
            }

            this.checkAttributesSelect(id, value);
        }

        // ---------------------------------------
    });
});
