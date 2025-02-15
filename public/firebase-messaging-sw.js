// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.
importScripts('https://www.gstatic.com/firebasejs/9.14.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging-compat.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
firebase.initializeApp({
    apiKey: "AIzaSyDm7wGbBwYkZJlpz6qkWDUu8IgstBEihfM",
    authDomain: "teamyard-d5c53.firebaseapp.com",
    projectId: "teamyard-d5c53",
    storageBucket: "teamyard-d5c53.appspot.com",
    messagingSenderId: "631436974234",
    appId: "1:631436974234:web:fb2efdf1d5e5103298e723",
    measurementId: "G-KCR99ZDQE8"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    clients.matchAll({
        type: 'window',
        includeUncontrolled: true
    })
    .then(function (windowClients) {
        for (let i = 0; i < windowClients.length; i++) {
            windowClients[i].postMessage(payload);
        }
    });
});

self.addEventListener("notificationclick", function (event) {
    console.log('notificationclick', event);
    var urlToRedirect = event.notification.data;
    event.notification.close();
    event.waitUntil(self.clients.openWindow(urlToRedirect));
});