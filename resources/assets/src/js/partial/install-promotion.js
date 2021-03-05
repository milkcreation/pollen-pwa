'use strict'

import 'regenerator-runtime/runtime.js'

let displayMode = 'browser tab',
    deferredPrompt,
    boxes = document.getElementsByClassName('PwaInstallPromotion'),
    closers = document.getElementsByClassName('PwaInstallPromotion-close')

window.addEventListener('DOMContentLoaded', () => {
  const mql = window.matchMedia('(display-mode: standalone)')

  if ('standalone' in navigator) {
    displayMode = 'standalone-ios'
  }

  if (mql.matches) {
    displayMode = 'standalone'
  }

  mql.addEventListener('change', e => {
    if (e.matches) {
      displayMode = 'standalone'
    }
  })
})

window.addEventListener('beforeinstallprompt', e => {
  e.preventDefault()

  if (getVisited()) {
    return
  }

  if (navigator.getInstalledRelatedApps) {
    navigator.getInstalledRelatedApps().then(relatedApps => {
      if (relatedApps.length === 0) {
        deferredPrompt = e

        Array.from(boxes).forEach(box => {
          box.classList.toggle('show', true)
        })
      }
    })
  } else {
    deferredPrompt = e
    Array.from(boxes).forEach(box => {
      box.classList.toggle('show', true)
    })
  }
})

Array.from(document.getElementsByClassName('PwaInstallPromotion-button')).forEach(button => {
  button.addEventListener('click', e => {
    e.preventDefault()

    if (deferredPrompt === undefined) {
      return
    }

    deferredPrompt.prompt()

    deferredPrompt.userChoice.then(choice => {
      if (choice.outcome === 'accepted') {
        window.deferredPrompt = null
        setVisited()

        Array.from(boxes).forEach(box => {
          box.classList.toggle('show', false)
        })
      } else {
        console.log('User dismissed the install prompt')
      }
    })
  })
})

window.addEventListener('appinstalled', () => {
  Array.from(boxes).forEach(box => {
    box.classList.toggle('show', false)
  })
  unsetVisited()
})

Array.from(closers).forEach(closer => {
  closer.addEventListener('click', e => {
    e.preventDefault()
    setVisited()
    Array.from(boxes).forEach(box => {
      box.classList.toggle('show', false)
    })
  })
})

const getVisited = () => {
  return localStorage.getItem('pwa-install-prompt') === 'true'
}

const setVisited = () => {
  localStorage.setItem('pwa-install-prompt', 'true')
}

const unsetVisited = () => {
  localStorage.setItem('pwa-install-prompt', 'false')
}