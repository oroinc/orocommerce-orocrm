define(function(require) {
    'use strict';

    const $ = require('jquery');
    const BaseView = require('oroui/js/app/views/base/view');

    const WarningSettingsView = BaseView.extend({
        $messageContainer: null,
        $selectStrategy: null,
        rootMessage: null,
        eachMessage: null,
        currentStrategy: null,
        defaultStrategy: null,

        events: {
            'change select': '_handleChangeSelect'
        },

        listen: {
            'strategy-creation-account:changeUseDefault mediator': '_handleChangeUseDefault'
        },

        /**
         * @inheritdoc
         */
        constructor: function WarningSettingsView(options) {
            WarningSettingsView.__super__.constructor.call(this, options);
        },

        /**
         * @inheritdoc
         */
        initialize: function(options) {
            this.$messageContainer = this.$el.find(options.warningContainer);
            this.$selectStrategy = this.$el.find('select');

            this.rootMessage = options.rootMessage;
            this.eachMessage = options.eachMessage;
            this.defaultStrategy = options.defaultStrategy;
            this.currentStrategy = this._getSelectedStrategy();

            this._hideMessages();
        },

        _handleChangeSelect: function(e) {
            const strategy = $(e.target).val();
            this._changeStrategy(strategy);
        },

        _handleChangeUseDefault: function(isDefault) {
            if (isDefault === true) {
                this.$selectStrategy.val(this.defaultStrategy).trigger('change');

                this._changeStrategy(this.defaultStrategy);
            } else {
                this._changeStrategy(this._getSelectedStrategy());
            }
        },

        _changeStrategy: function(strategy) {
            this._hideMessages();
            if (strategy !== this.currentStrategy) {
                this._showMessage(strategy);
            }
        },

        _showMessage: function(strategy) {
            if (strategy === 'root') {
                this.$messageContainer.text(this.rootMessage);
            } else {
                this.$messageContainer.text(this.eachMessage);
            }

            this.$messageContainer.show();
        },

        _hideMessages: function() {
            this.$messageContainer.hide();
        },

        _getSelectedStrategy: function() {
            return this.$selectStrategy.val();
        }
    });

    return WarningSettingsView;
});
