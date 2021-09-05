import UIkitIcons from 'uikit/dist/js/uikit-icons';

UIkit.use(UIkitIcons);

try {
    window.$ = window.jQuery = require('jquery');
    window.UIkit = require('uikit');

    require('datatables.net');
    require('datatables.net-buttons');

    require('flatpickr');
    require('flatpickr/dist/l10n/hu');

    window.DarkReader = require('darkreader');
} catch (e) {
    console.error('Error loading module:', e);
}
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
