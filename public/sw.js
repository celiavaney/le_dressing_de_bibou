// // sw.js

// // Cache assets during installation
// self.addEventListener("install", function (event) {
//   event.waitUntil(
//     caches.open("my-cache-v1").then(function (cache) {
//       return cache.addAll([
//         "/",
//         "/index.html",
//         "/styles/main.css",
//         "/scripts/main.js",
//         "/images/logo.png",
//       ]);
//     })
//   );
// });

// // Intercept fetch events and serve cached responses
// self.addEventListener("fetch", function (event) {
//   event.respondWith(
//     caches.match(event.request).then(function (response) {
//       return response || fetch(event.request);
//     })
//   );
// });

// // Service worker registration succeeded
// console.log("Service Worker registered");

//lors de l'installation
self.addEventListener("install", (evt) => {
  console.log("install evt", evt);
});

//capture des events
self.addEventListener("fetch", (evt) => {
  console.log("events captures : ");
  console.log("fetch evt sur url", evt.request.url);
});
