/**
 * Notifications
 *
 * @class
 * @param  {Object} options Optional configuration options
 * @return {Notifications} Notifications main instance
 */
var Notifications = (function(options) {

    /**
     * Use the jQuery Growl library
     * @type {string}
     */
    const THEME_GROWL = "growl";

    /**
     * Use the noty library
     * @type {string}
     */
    const THEME_NOTY = "noty";


    var self = this;

    /**
     * Options
     * @type {Object}
     */
    this.opts = $.extend({
        delay: 5000,
        timeout: 2000,
        seen: false,
        theme: THEME_GROWL,
        counters: []
    }, options);


    /**
     * Already displayed notifications cache
     * @type {Array}
     */
    this.displayed = [];

    /**
     * Polls the server
     */
    this.poll = function() {
        $.ajax({
            url: this.opts.url,
            type: "GET",
            data: {
                seen: this.opts.seen ? 1 : 0
            },
            success: function(data) {
                $.each(data, function (index, object) {
                    if (self.displayed.indexOf(object.id) !== -1) {
                        return;
                    }
                    switch (self.opts.theme) {
                        case THEME_NOTY:
                            noty($.extend({
                                text        : object.title,
                                type        : 'notification',
                                dismissQueue: false,
                                timeout     : 5000,
                                layout      : 'topRight',
                                theme       : 'defaultTheme',
                                callback    : {
                                    onCloseClick: function() {
                                        document.location = object.url;
                                    }
                                }
                            }, self.opts.options));
                            break;

                        case THEME_GROWL:
                        default:
                            $.growl.error($.extend({
                                title: object.title,
                                message: object.description,
                                url: object.url
                            }, self.opts.options));
                            break;
                    }
                    self.displayed.push(object.id);
                });

                // Update all counters
                for (var i = 0; i < self.opts.counters.length; i++) {
                    if ($(self.opts.counters[i]).text() != data.length) {
                        $(self.opts.counters[i]).text(data.length);
                    }
                }
            },
            dataType: "json",
            complete: setTimeout(function() {
                self.poll(opts)
            }, opts.delay),
            timeout: opts.timeout
        });
    };

    // Fire the initial poll
    this.poll();

});