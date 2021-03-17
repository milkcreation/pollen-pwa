/* global PWA */
'use strict'

if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register(PWA.url).then(function (registration) {
      console.log('ServiceWorker registration successful with scope: ', registration.scope)
      registration.update()
    }, function (err) {
      console.log('ServiceWorker registration failed: ', err)
    })
  })
}


