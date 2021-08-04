define(function(require) {
    'use strict';

    const $ = require('jquery');
    const BaseView = require('oroui/js/app/views/base/view');
    const mediator = require('oroui/js/mediator');

    const UseSefaultView = BaseView.extend({
        events: {
            'change input[type="checkbox"]': '_handleChangeUseDefault'
        },

        /**
         * @inheritdoc
         */
        constructor: function UseSefaultView(options) {
            UseSefaultView.__super__.constructor.call(this, options);
        },

        _handleChangeUseDefault: function(e) {
            mediator.trigger('strategy-creation-account:changeUseDefault', $(e.target).is(':checked'));
        }
    });

    return UseSefaultView;
});
