/* global PWA */
'use strict'

// Incrementing OFFLINE_VERSION will kick off the install event and force
// previously cached resources to be updated from the network.
const OFFLINE_VERSION = 1,
    CACHE = PWA.cache,
    CACHE_ENABLED = CACHE.enabled || false,
    CACHE_KEY = CACHE.key || undefined,
    CACHE_WHITELIST = CACHE.whitelist || [],
    CACHE_BLACKLIST = CACHE.blacklist || [],
    OFFLINE_URL = PWA['offline_url'],
    NAV_PRELOAD = PWA['navigation_preload']

self.addEventListener("install", (e) => {
  e.waitUntil(
      (async () => {
        const cache = await caches.open(CACHE_KEY)

        // Setting {cache: 'reload'} in the new request will ensure that the
        // response isn't fulfilled from the HTTP cache; i.e., it will be from
        // the network.
        for (const url of CACHE_WHITELIST) {
          await cache.add(new Request(url, {cache: 'reload'}))
        }
      })()
  )
  // Force the waiting service worker to become the active service worker.
  self.skipWaiting()
})

self.addEventListener("activate", (e) => {
  /*  Suppression du cache */
  e.waitUntil(
      caches.keys().then((keys) => {
        return Promise.all(keys.map(function (key) {
          if (CACHE_KEY !== key) {
            return caches.delete(key)
          }
        }))
      })
  )

  e.waitUntil(
      (async () => {
        // Enable navigation preload if it's supported.
        // See https://developers.google.com/web/updates/2017/02/navigation-preload
        if (NAV_PRELOAD && "navigationPreload" in self.registration) {
          await self.registration.navigationPreload.enable()
        } else {
          await self.registration.navigationPreload.disable()
        }
      })()
  )

  // Tell the active service worker to take control of the page immediately.
  self.clients.claim()
})

self.addEventListener("fetch", (e) => {
  const matchUrl = (url) => {
        return !e.request.url.match(url);
      },
      caching = async (request, response) => {
        // Check if cache is enabled
        if (CACHE_ENABLED === false) {
          throw 'Caching is not enabled'
        }

        // Check current request url is in the cache blacklist.
        if (!CACHE_BLACKLIST.every(matchUrl)) {
          throw 'Current request is excluded from cache.'
        }

        // Check if current request url protocol isn't http or https
        if (!request.url.match(/^(http|https):\/\//i)) {
          throw 'Only http(s) request is allowed to be cached.'
        }

        // Check if current request url is from an external domain.
        if (new URL(request.url).origin !== location.origin) {
          throw 'Only local domain request is allowed to be cached.'
        }

        if (e.request.method !== 'GET') {
          throw 'Only the GET request method is allowed to be cached.'
        }

        const cache = await caches.open(CACHE_KEY)

        await cache.put(e.request, response.clone())
      }

  // We only want to call event.respondWith() if this is a navigation request
  // for an HTML page.
  if (e.request.mode === "navigate") {
    e.respondWith(
        (async () => {
          try {
            const preloadResponse = await e.preloadResponse,
                response = preloadResponse ? preloadResponse : await fetch(e.request)

            try {
              await caching(e.request, response)
            } catch (e) {
              console.log(e)
            }

            return response
          } catch (error) {
            const cache = await caches.open(CACHE_KEY)

            return await cache.match(e.request) || await cache.match(OFFLINE_URL)
          }
        })()
    )
  } else {
    // If our if() condition is false, then this fetch handler won't intercept the
    // request. If there are any other fetch handlers registered, they will get a
    // chance to call event.respondWith(). If no fetch handlers call
    // event.respondWith(), the request will be handled by the browser as if there
    // were no service worker involvement.
    e.respondWith(
        (async () => {
          try {
            const preloadResponse = await e.preloadResponse,
                response = preloadResponse ? preloadResponse : await fetch(e.request)

            try {
              await caching(e.request, response)
            } catch (e) {
              //console.log(e)
            }

            return response
          } catch (error) {
            const cache = await caches.open(CACHE_KEY)

            return await cache.match(e.request) /*|| await cache.match(OFFLINE_URL)*/
          }
        })()
    )
  }
})

self.addEventListener('push', function (e) {
  if (!(self.Notification && self.Notification.permission === 'granted')) {
    return
  }

  const sendNotification = body => {
    // you could refresh a notification badge here with postMessage API
    const title = "Web Push example"

    return self.registration.showNotification(title, {
      body,
    })
  }

  if (e.data) {
    const message = e.data.text()
    e.waitUntil(sendNotification(message))
  }
})