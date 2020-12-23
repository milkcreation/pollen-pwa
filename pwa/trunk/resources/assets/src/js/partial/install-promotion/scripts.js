'use strict'

import 'regenerator-runtime/runtime.js'

let displayMode = 'browser tab',
    deferredPrompt,
    boxes = document.getElementsByClassName('PwaInstallPromotion')

window.addEventListener('DOMContentLoaded', () => {
    const mql = window.matchMedia('(display-mode: standalone)')

    if ('standalone' in navigator) {
        displayMode = 'standalone-ios'
    }

    if (mql.matches) {
        displayMode = 'standalone'
    }

    mql.addEventListener('change', (e) => {
        if (e.matches) {
            displayMode = 'standalone'
        }
    })
})

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault()

    if (navigator.getInstalledRelatedApps) {
        navigator.getInstalledRelatedApps().then(relatedApps => {
            if (relatedApps.length === 0) {
                deferredPrompt = e

                Array.from(boxes).forEach((box) => {
                    box.classList.toggle('show', true)
                })
            }
        })
    } else {
        deferredPrompt = e

        Array.from(boxes).forEach((box) => {
            box.classList.toggle('show', true)
        })
    }
})

Array.from(document.getElementsByClassName('PwaInstallPromotion-button')).forEach((button) => {
    button.addEventListener('click', (e) => {
        e.preventDefault()

        if (deferredPrompt === undefined) {
            return
        }

        deferredPrompt.prompt()

        deferredPrompt.userChoice.then((choice) => {
            if (choice.outcome === 'accepted') {
                window.deferredPrompt = null

                Array.from(boxes).forEach((box) => {
                    box.classList.toggle('show', false)
                })
            } else {
                console.log('User dismissed the install prompt')
            }
        })
    })
})

window.addEventListener('appinstalled', () => {
    Array.from(boxes).forEach((box) => {
        box.classList.toggle('show', false)
    })
})