define([
    'jquery',
    'mage/template'
], function (jQuery, mageTemplate) {

    var SCOPE_CONTEXT = 1,
            SCOPE_GLOBAL = 2,
            SUCCESS = 1,
            WARNING = 2,
            ERROR = 3,
            _templateContainer = '<div id="messages"><div class="messages Temu-messages"></div></div>',
            _templates = {
                global: '<div class="message"><div><%= data %></div></div>',
                success: '<div class="message message-success success"><div data-ui-id="messages-message-success"><%= data %></div></div>',
                warning: '<div class="message message-warning warning"><div data-ui-id="messages-message-warning"><%= data %></div></div>',
                error: '<div class="message message-error error"><div data-ui-id="messages-message-error"><%= data %></div></div>'
            };

    return {
        _container: '#anchor-content',

        _globalContainer: '#globalMessages',

        setContainer: function (container) {
            this._container = container;
        },

        add: function (message, scope, type) {

            let templateContainer;

            if (scope == SCOPE_GLOBAL) {
                templateContainer = jQuery(this._globalContainer).find('#messages');

                if (!templateContainer.length) {
                    jQuery(this._globalContainer).prepend(_templateContainer);
                    templateContainer = jQuery(this._globalContainer).find('#messages');
                }
            } else {
                templateContainer = jQuery(this._container).find('#messages');

                if (!templateContainer.length) {
                    var pageActions = jQuery(this._container).find('.page-main-actions');
                    if (pageActions.length) {
                        pageActions.after(_templateContainer);
                    } else {
                        jQuery(this._container).prepend(_templateContainer);
                    }
                    templateContainer = jQuery(this._container).find('#messages');
                }
            }

            let template = _templates.global;

            if (type === SUCCESS) {
                template = _templates.success;
            } else if (type == WARNING) {
                template = _templates.warning;
            } else if (type == ERROR) {
                template = _templates.error;
            }

            const messageBlock = mageTemplate(template, {
                data: message
            });

            templateContainer.find('.messages').prepend(messageBlock);

            if (scope == SCOPE_GLOBAL) {
                CommonObj.updateFloatingHeader();
            }

            return this;
        },

        addSuccess: function (message) {
            return this.add(message, SCOPE_CONTEXT, SUCCESS);
        },

        addNotice: function (message) {
            return this.add(message, SCOPE_CONTEXT);
        },

        addWarning: function (message) {
            return this.add(message, SCOPE_CONTEXT, WARNING);
        },

        addError: function (message) {
            return this.add(message, SCOPE_CONTEXT, ERROR);
        },

        addGlobalSuccess: function (message) {
            return this.add(message, SCOPE_GLOBAL, SUCCESS);
        },

        addGlobalNotice: function (message) {
            return this.add(message, SCOPE_GLOBAL);
        },

        addGlobalWarning: function (message) {
            return this.add(message, SCOPE_GLOBAL, WARNING);
        },

        addGlobalError: function (message) {
            return this.add(message, SCOPE_GLOBAL, ERROR);
        },

        clear: function () {
            jQuery(this._container).find('#messages > .messages').empty();
        },

        clearGlobal: function () {
            jQuery(this._globalContainer).find('.messages').empty();
            CommonObj.updateFloatingHeader();
        },

        clearAll: function () {
            this.clear();
        },

        clearAllGlobal: function () {
            this.clearGlobal();
        }
    };
});
