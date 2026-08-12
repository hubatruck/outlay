import $ from "jquery";
window.$ = window.jQuery = $;

import UIkitIcons from 'uikit/dist/js/uikit-icons';
import UIkit from 'uikit';
window.UIkit = UIkit;
UIkit.use(UIkitIcons);

import flatpickr from 'flatpickr';
import 'flatpickr/dist/l10n/hu';
window.flatpickr = flatpickr;
$.fn.flatpickr = function (options) {
    return this.each(function () {
        flatpickr(this, options);
    });
};

import * as DarkReader from 'darkreader';
window.DarkReader = DarkReader;

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
