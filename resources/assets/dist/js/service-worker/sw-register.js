/* global pwaSW */
'use strict'

window.addEventListener('load', () => {
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register(pwaSW.url).then(function (registration) {
            console.log('ServiceWorker registration successful with scope: ', registration.scope)
            registration.update()
        }, function (err) {
            console.log('ServiceWorker registration failed: ', err)
        })
    }
})


