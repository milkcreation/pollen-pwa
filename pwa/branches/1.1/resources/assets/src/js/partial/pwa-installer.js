'use strict'

import 'regenerator-runtime/runtime.js'

let displayMode = 'browser tab',
    deferredPrompt,
    banners = document.querySelectorAll('[data-pwa-installer="banner"]'),
    closers = document.querySelectorAll('[data-pwa-installer="close"]'),
    bannersTimeout = {}

const setBannerPrompt = (banner, index) => {
      if (!getDismissed()) {
        banner.classList.toggle('show', true)
      }

      setBannerHandling(banner)

      const timeout = banner.dataset.timeout || 0
      if (timeout) {
        setBannerTimeout(banner, index, timeout)
      }
    },
    setBannerHandling = banner => {
      const handler = banner.querySelector('[data-pwa-installer="handler"]')

      if (handler) {
        handler.style.display = 'block';

        handler.addEventListener('click', e => {
          e.preventDefault()

          if (banner.classList.contains('show')) {
            banner.classList.toggle('show', false)
          } else {
            banner.classList.toggle('show', true)
          }
        })
      }
    },
    setBannerTimeout = (banner, index, timeout) => {
      bannersTimeout[index] = setTimeout(() => {
        banner.classList.toggle('show', false)
      }, timeout)

      banner.addEventListener('mouseenter', () => {
        clearTimeout(bannersTimeout[index])
      })

      banner.addEventListener('mouseleave', () => {
        setBannerTimeout(banner, index, timeout)
      })
    }

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

  if (navigator.getInstalledRelatedApps) {
    navigator.getInstalledRelatedApps().then(relatedApps => {
      if (relatedApps.length === 0) {
        if (deferredPrompt === undefined) {
          Array.from(banners).forEach((banner, i) => {
            setBannerPrompt(banner, i)
          })
        }
        deferredPrompt = e
      }
    })
  } else {
    if (deferredPrompt === undefined) {
      Array.from(banners).forEach((banner, i) => {
        setBannerPrompt(banner, i)
      })
    }
    deferredPrompt = e
  }
})

Array.from(document.querySelectorAll('[data-pwa-installer="install"]')).forEach(button => {
  button.addEventListener('click', e => {
    e.preventDefault()

    if (deferredPrompt === undefined) {
      return
    }

    deferredPrompt.prompt()

    deferredPrompt.userChoice.then(choice => {
      if (choice.outcome === 'accepted') {
        window.deferredPrompt = null
        setDismissed()

        Array.from(banners).forEach(banner => {
          banner.classList.toggle('show', false)
        })
      } else {
        console.log('User dismissed the install prompt')
      }
    })
  })
})

window.addEventListener('appinstalled', () => {
  Array.from(banners).forEach(banner => {
    banner.classList.toggle('show', false)
  })
  clearDismissed()
})

Array.from(closers).forEach(closer => {
  closer.addEventListener('click', e => {
    e.preventDefault()
    setDismissed()
    Array.from(banners).forEach(banner => {
      banner.classList.toggle('show', false)
    })
  })
})

const getDismissed = () => {
  return localStorage.getItem('pwa-installer-dismiss') === 'true'
}

const setDismissed = () => {
  localStorage.setItem('pwa-installer-dismiss', 'true')
}

const clearDismissed = () => {
  localStorage.setItem('pwa-installer-dismiss', 'false')
}