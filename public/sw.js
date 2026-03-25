self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const data = event.data ? event.data.json() : {};

    const title = data.title || 'Pemberitahuan Baru';
    const options = {
        body: data.body || 'Anda memiliki pesan baru.',
        icon: data.icon || '/logo.png', // Fallback icon path
        badge: '/logo.png',
        data: {
            url: data.action_url || '/'
        }
    };
    
    // Support custom data url mapping like in our toWebPush
    if(data.data && data.data.url) {
        options.data.url = data.data.url;
    }

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    // Check if URL is passed in notification data
    const url = event.notification.data ? event.notification.data.url : '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            // Check if there is already a window/tab open with the target URL
            for (let i = 0; i < windowClients.length; i++) {
                const client = windowClients[i];
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            // If not, open a new window/tab with the URL
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

// A basic fetch listener is required for Chrome to prompt PWA installation
self.addEventListener('fetch', function(event) {
    // We just return the default fetch so it doesn't break anything
    // (You can add offline caching later)
    return event.respondWith(fetch(event.request));
});
