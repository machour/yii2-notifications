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
                    duration: self.opts.delay,
                    title: object.title,
                    message: object.description,
                    url: object.url,
                    style: getType(object.type),
                }, self.opts.options));
            }
        },

        /**
         * Notie
         * @see https://jaredreich.com/projects/notie/
         */
        notie: {
            types: {
                'default': 4,
                error: 3,
                warning: 2,
                success: 1
            },
            show: function (object) {
                notie.alert(getType(object.type), object.description, self.opts.delay / 999);
            }
        },

        /**
         * NotifIt!
         * @see http://naoxink.hol.es/notifIt/
         */
        notifit: {
            types: {
                'default': 'info'
            },
            show: function (object) {
                notif($.extend({
                    timeout: self.opts.delay,
                    clickable: true,
                    multiline: true,
                    msg: "<b>" + object.title + "</b><br /><br />" + object.description,
                    type: getType(object.type)
                }, self.opts.options));
                $("#ui_notifIt").click(function(e) {
                    document.location = object.url;
                });
            }
        },

        /**
         * Notify.js
         * @see https://notifyjs.com/
         */
        notify: {
            types: {
                warning: 'warn',
                'default': 'info'
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
                'default': 'information'
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
         * PNotify
         * @see http://sciactive.com/pnotify/
         */
        pnotify: {
            types: {
                warning: 'notice',
                'default': 'info'
            },
            show: function (object) {
                new PNotify($.extend({
                    title: object.title,
                    text: '<a href="' + object.url + '" style="text-decoration: none;">' + object.description + '</a>',
                    type: getType(object.type),
                    delay: self.opts.delay
                }, self.opts.options));
            }
        },

        /**
         * Toastr
         * @see https://codeseven.github.io/toastr/
         */
        toastr: {
            types: {
                'default': 'info'
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
        seenUrl: '', // Overwritten by widget
        deleteUrl: '', // Overwritten by widget
        pollInterval: 5000,
        pollSeen: false,
        xhrTimeout: 2000,
        delay: 5000,
        theme: null,
        counters: [],
        listSelector: null,
        listItemTemplate:
            '<div class="row">' +
                '<div class="col-xs-10">' +
                    '<div class="title">{title}</div>' +
                    '<div class="description">{description}</div>' +
                    '<div class="timeago">{timeago}</div>' +
                '</div>' +
                '<div class="col-xs-2">' +
                    '<div class="actions pull-right">{seen}{delete}</div>' +
                '</div>' +
            '</div>',
        listItemBeforeRender: function (elem) {
            return elem;
        }
    }, options);

    /**
     * Already displayed notifications cache
     * @type {Array}
     */
    this.displayed = [];

    /**
     * Renders a notification row
     *
     * @param object The notification instance
     * @returns {jQuery|HTMLElement|*}
     */
    this.renderRow = function (object) {
        var keywords = ['id', 'title', 'description', 'url', 'type'];
        var ret, html = self.opts.listItemTemplate;

        html = '<div class="notification notification-{type} ' +
            (object.seen ? 'notification-seen' : 'notification-unseen') +
            '" data-route="{url}" data-id="{id}">' +
                html +
                '</div>';

        for (var i = 0; i < keywords.length; i++) {
            html = html.replace(new RegExp('{' + keywords[i] + '}', 'g'), object[keywords[i]]);
        }

        html = html.replace(/\{seen}/g, '<span class="notification-seen fa fa-check"></span>');
        html = html.replace(/\{delete}/g, '<span class="notification-delete fa fa-close"></span>');
        html = html.replace(/\{timeago}/g, '<span class="notification-timeago"></span>');
        ret = $(html);
        ret.find('.notification-seen').click(function() {
            self.markSeen($(this).parents('.notification').data('id'));
            return false;
        });
        ret.find('.notification-timeago').text($.timeago(object['date']));
        ret.find('.notification-delete').click(function() {
            self.delete($(this).parents('.notification').data('id'));
            return false;
        });
        return ret;
    };

    /**
     * Marks a notification as seen
     * @param {int} id The notification id
     */
    this.markSeen = function (id) {
        $.get(this.opts.seenUrl, {id: id}, function () {

        });
    };

    /**
     * Deletes a notification
     * @param {int} id The notification id
     */
    this.delete = function (id) {
        $.get(this.opts.deleteUrl, {id: id}, function () {
            $('.notification[data-id=' + id + ']').hide();
        });
    };

    /**
     * Translates a native type to a theme type
     *
     * @param type
     * @returns The translated theme type
     */
    function getType(type) {

        var types = this.themes[this.opts.theme].types;
        var translation;

        if (typeof types !== "undefined") {
            translation = types[type];
            if (typeof translation !== "undefined") {
                return translation;
            }
        }
        return type;
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
                var elem;
                $.each(data, function (index, object) {
                    if (self.displayed.indexOf(object.id) !== -1) {
                        return;
                    }

                    self.displayed.push(object.id);

                    if (self.opts.theme !== null) {
                        if (typeof engine !== "undefined") {
                            engine.show(object);
                        } else {
                            console.warn("Unknown engine: " + self.opts.theme);
                        }
                    }

                    if (self.opts.listSelector !== null) {
                        elem = self.renderRow(object);
                        elem = self.opts.listItemBeforeRender(elem);
                        elem.click(function() {
                            document.location = $(this).data('route');
                        }).appendTo(self.opts.listSelector);
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
