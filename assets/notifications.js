/**
 * Notifications
 *
 * @class
 * @param  {Object} options Optional configuration options
 * @return {Notifications} Notifications main instance
 */
var Notifications = (function(options) {

    /**
     * The declared UI libraries
     *
     * Each property of this variable must be an object with the `show` callback
     * field defined, and an optional `types` translation map that will be used
     * to translate natives types into the used library notification type.
     *
     * @type Object
     */
    this.themes = {
        /**
         * jQuery Growl
         * @see https://github.com/ksylvest/jquery-growl
         */
        growl: {
            types: {
                success: 'notice'
            },
            show: function (object) {
                $.growl($.extend({
                    title: object.title,
                    message: object.description,
                    url: object.url,
                    style: getType(object.type),
                }, self.opts.options));
            }
        },

        /**
         * Notify.js
         * @see https://notifyjs.com/
         */
        notify: {
            types: {
                warning: 'warn',
                default: 'info'
            },
            show: function (object) {
                $.notify(object.title, getType(object.type), $.extend({
                    autoHideDelay: self.opts.delay,
                }, self.opts.options));
            }
        },

        /**
         * Noty
         * @see http://ned.im/noty/
         */
        noty: {
            types: {
                default: 'information'
            },
            show: function (object) {
                noty($.extend({
                    text: object.title,
                    type: getType(object.type),
                    dismissQueue: false,
                    timeout: self.opts.delay,
                    layout: 'topRight',
                    theme: 'defaultTheme',
                    callback: {
                        onCloseClick: function () {
                            document.location = object.url;
                        }
                    }
                }, self.opts.options));
            }
        },

        /**
         * Toastr
         * @see https://codeseven.github.io/toastr/
         */
        toastr: {
            types: {
                default: 'info'
            },
            show: function (object) {
                toastr[getType(object.type)](object.description, object.title, $.extend({
                    timeOut: self.opts.delay,
                    onclick: function() {
                        document.location = object.url;
                    }
                }, self.opts.options));
            }
        }
    };


    var self = this;

    /**
     * Options
     * @type {Object}
     */
    this.opts = $.extend({
        pollInterval: 5000,
        pollSeen: false,
        xhrTimeout: 2000,
        delay: 5000,
        theme: 'growl',
        counters: []
    }, options);


    /**
     * Already displayed notifications cache
     * @type {Array}
     */
    this.displayed = [];

    /**
     * Translates a native type to a theme type
     *
     * @param type
     * @returns The translated theme type
     */
    function getType(type) {

        var types = this.themes[this.opts.theme].types;
        var translation;

        if (typeof types !== "undefined" ||
            (translation = types[type] && typeof translation === "undefined")) {
            return type;
        }

        return translation;
    }

    /**
     * Polls the server
     */
    this.poll = function() {
        $.ajax({
            url: this.opts.url,
            type: "GET",
            data: {
                seen: this.opts.pollSeen ? 1 : 0
            },
            success: function(data) {
                var engine = self.themes[self.opts.theme];
                $.each(data, function (index, object) {
                    if (self.displayed.indexOf(object.id) !== -1) {
                        return;
                    }
                    if (typeof engine !== "undefined") {
                        engine.show(object);
                        self.displayed.push(object.id);
                    } else {
                        console.warn("Unknown engine: " + self.opts.theme);
                    }
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
            }, opts.pollInterval),
            timeout: opts.xhrTimeout
        });
    };

    // Fire the initial poll
    this.poll();

});