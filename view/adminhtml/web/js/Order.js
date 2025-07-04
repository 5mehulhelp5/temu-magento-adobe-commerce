define([
    'Temu/Common'
], function () {

    Order = Class.create(Common, {

        // ---------------------------------------

        initialize: function (gridIds) {
            this.gridIds = gridIds ? eval(gridIds) : [];
            this.hideTitleAttribute($$('div .note_icon'));
        },

        initializeGrids: function () {
            var self = OrderObj;

            for (var i = 0; i < self.gridIds.length; i++) {
                var currentGridId = self.gridIds[i];

                var tempGrid = window[currentGridId + 'JsObject'];
                if (!(tempGrid instanceof varienGrid)) {
                    continue;
                }

                if (typeof self[currentGridId] != 'undefined') {
                    // already initialized
                    continue;
                }

                self[currentGridId] = tempGrid.rowClickCallback;
                tempGrid.rowClickCallback = self.gridRowClickCallback;
            }
        },

        disableGridCallback: function (gridId) {
            var tempGrid = window[gridId + 'JsObject'];

            if (!(tempGrid instanceof varienGrid)) {
                return;
            }

            tempGrid.rowClickCallback = '';
        },

        restoreGridCallback: function (gridId) {
            var self = OrderObj;
            var tempGrid = window[gridId + 'JsObject'];

            if (!(tempGrid instanceof varienGrid)) {
                return;
            }

            tempGrid.rowClickCallback = self.gridRowClickCallback;
        },

        gridRowClickCallback: function (grid, event) {
            if (['a', 'select', 'option'].indexOf(Event.element(event).tagName.toLowerCase()) != -1) {
                return;
            }

            var self = OrderObj;
            var tdElement = Event.findElement(event, 'td');

            if ($(tdElement).down('input')) {
                self[grid.containerId](grid, event);
            }
        },

        // ---------------------------------------

        viewOrderHelp: function (rowId, data) {
            var row = $('grid_help_icon_open_' + rowId).up('tr');
            var grid = row.up('table');
            var gridId = grid.id.replace('_table', '');

            OrderObj.disableGridCallback(gridId);

            $('grid_help_icon_open_' + rowId).hide();
            $('grid_help_icon_close_' + rowId).show();

            if ($('grid_help_content_' + rowId) !== null) {
                $('grid_help_content_' + rowId).show();

                // Restore grid callback
                // ---------------------------------------
                setTimeout(function () {
                    OrderObj.restoreGridCallback(gridId);
                }, 150);
                // ---------------------------------------
                return;
            }

            var html = OrderObj.createHelpTitleHtml(rowId);

            data = eval(base64_decode(data));
            for (var i = 0; i < data.length; i++) {
                html += OrderObj.createHelpActionHtml(data[i]);
            }

            html += OrderObj.createHelpViewAllLogHtml(rowId, gridId);

            row.insert({
                after: '<tr id="grid_help_content_' + rowId + '" class="grid_help_content"><td class="help_line" colspan="' + ($(row).childElements().length) + '">' + html + '</td></tr>'
            });

            setTimeout(function () {
                OrderObj.restoreGridCallback(gridId);
            }, 150);
        },

        hideOrderHelp: function (rowId) {
            var row = $('grid_help_icon_open_' + rowId).up('tr');
            var grid = row.up('table');
            var gridId = grid.id.replace('_table', '');

            OrderObj.disableGridCallback(gridId);

            if ($('grid_help_content_' + rowId) != null) {
                $('grid_help_content_' + rowId).hide().remove();
            }

            $('grid_help_icon_open_' + rowId).show();
            $('grid_help_icon_close_' + rowId).hide();

            setTimeout(function () {
                OrderObj.restoreGridCallback(gridId);
            }, 150);
        },

        createHelpTitleHtml: function (rowId) {
            var nativeOrderNumber = $('grid_help_icon_open_' + rowId).up('td').next().innerHTML;
            var orderTitle = nativeOrderNumber.replace(/<[^>]+>/g, '');
            var closeHtml = '<a href="javascript:void(0);" onclick="OrderObj.hideOrderHelp(' + rowId + ');" title="' + Temu.translator.translate('Close') + '"><span class="hl_close icon-close"></span></a>';

            return '<div class="hl_header"><span class="hl_title">' + orderTitle + '</span>' + closeHtml + '</div>';
        },

        createHelpActionHtml: function (action) {
            var classContainer = 'hl_container';
            if (action.type == Temu.php.constant('M2E_Temu_Model_Log_AbstractModel::TYPE_SUCCESS')) {
                classContainer += ' hl_container_success';
            } else if (action.type == Temu.php.constant('M2E_Temu_Model_Log_AbstractModel::TYPE_WARNING')) {
                classContainer += ' hl_container_warning';
            } else if (action.type == Temu.php.constant('M2E_Temu_Model_Log_AbstractModel::TYPE_INFO')) {
                classContainer += ' hl_container_info';
            } else if (action.type == Temu.php.constant('M2E_Temu_Model_Log_AbstractModel::TYPE_ERROR')) {
                classContainer += ' hl_container_error';
            }

            var type = '<span style="color: green;">' + Temu.translator.translate('Success') + '</span>';
            if (action.type == Temu.php.constant('M2E_Temu_Model_Log_AbstractModel::TYPE_INFO')) {
                type = '<span style="color: blue;">' + Temu.translator.translate('Info') + '</span>';
            } else if (action.type == Temu.php.constant('M2E_Temu_Model_Log_AbstractModel::TYPE_WARNING')) {
                type = '<span style="color: orange;">' + Temu.translator.translate('Warning') + '</span>';
            } else if (action.type == Temu.php.constant('M2E_Temu_Model_Log_AbstractModel::TYPE_ERROR')) {
                type = '<span style="color: red;">' + Temu.translator.translate('Error') + '</span>';
            }

            var html = '<div class="' + classContainer + '">';

            html += '<div class="hl_date">' + action.localized_date + '</div>';

            if (action.initiator != '') {
                html += '<div class="hl_action">' +
                        '<strong style="color: gray;">' + action.initiator + '</strong>&nbsp;&nbsp;' +
                        '</div>';
            }

            html += '<div style="clear: both"></div>';

            html += '<div style="padding-top: 3px;"><div style="margin-top: 7px;">';
            html += '<div class="hl_messages_type">' + type + '</div><div class="hl_messages_text">' + action.text + '</div>';
            html += '</div></div>';

            html += '</div>';

            return html;
        },

        createHelpViewAllLogHtml: function (rowId, gridId) {
            let url = Temu.url.get('log_order/index', {id: rowId});

            return '<div class="hl_footer"><a target="_blank" href="' + url + '">' + Temu.translator.translate('View Full Order Log') + '</a></div>';
        },

        hideTitleAttribute: function (elements) {

            elements.forEach(function (item) {

                var element = item.up('tr');

                item.observe('mouseover', function () {
                    if (element.readAttribute('title') !== null) {
                        element.writeAttribute('backup', element.readAttribute('title'));
                        element.writeAttribute('title', null);
                    }
                });

                item.observe('mouseout', function () {
                    element.writeAttribute('title', element.readAttribute('backup'));
                });
            });
        }

        // ---------------------------------------
    });
});
