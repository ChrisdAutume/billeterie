/**
 * This file contains the serviceworker used to allow offline access of the 'gichet' page
 * It is is the web/ directory because it have to be at the web root of the app to
 * control the whole app.
 *
 * To make it work, you have to refresh the gichet page twice
 * - First time to install the serviceWorker
 * - Second time so it can cache the gichet page
 * When serviceworker is installed and active, you should see
 * "Service worker active" in the js console.
 */

var CACHE_VERSION = 'v1';

// Put CDN used by your offline page here
var FETCH_CDN = [
    'maxcdn.bootstrapcdn.com',
    'code.ionicframework.com',
    'code.jquery.com',
    'cdnjs.cloudflare.com',
    'fonts.googleapis.com',
    'fonts.gstatic.com',
];
// We don't fetch on install because some of them needs auth cookies
var FETCH_LIST = [
    '/css/AdminLTE.min.css',
    '/css/skins/skin-blue.css',
    '/css/style.css',
    '/js/admin.min.js',
    '/_debugbar/assets/javascript',
    '/_debugbar/assets/stylesheets',
    '/admin/guichet',
    '/admin/guichet/',
    '/',
];

self.addEventListener('fetch', function(event) {
    let url = event.request.url;
    let pathname = new URL(url).pathname;
    let hostname = new URL(url).hostname;

    if (FETCH_LIST.indexOf(pathname) !== -1
        || FETCH_LIST.indexOf(url) !== -1
        || FETCH_CDN.indexOf(hostname) !== -1) {
        event.respondWith(
            caches.open(CACHE_VERSION).then((cache) => {
                return new Promise((resolve, reject) => {
                    var sent = false;

                    // By default try to fetch
                    fetch(event.request)
                    .then((response) => {
                        // On success, update cache and return the response
                        // console.log('cache updated', url)
                        cache.put(url, response.clone());

                        if(!sent) {
                            sent = true;
                            // console.log('Live version served', url)
                            return resolve(response)
                        }
                    })
                    .catch(() => {
                        cache.match(event.request)
                        .then((response) => {
                            // console.log('load from cache (fetch error)', url);
                            if(!sent) {
                                if (response) {
                                    sent = true;
                                    return resolve(response);
                                }
                                else {
                                    return reject('no-match');
                                }
                            }
                        })
                    })
                });
            })
        )
    }
});
