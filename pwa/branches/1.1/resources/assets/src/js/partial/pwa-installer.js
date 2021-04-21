'use strict'

import Observer from '@pollen-solutions/support/resources/assets/src/js/mutation-observer'

class PwaInstaller {
  constructor(el, options = {}) {
    this.debug = false

    this.initialized = false

    this.options = {
      classes: {
        title: 'PwaInstaller-title',
        content: 'PwaInstaller-content',
        close: 'PwaInstaller-close',
        button: 'PwaInstaller-switch',
        handler: 'PwaInstaller-handler',
      }
    }

    this.control = {
      title: 'title',
      content: 'content',
      close: 'close',
      button: 'button',
      handler: 'handler'
    }

    this.el = el
    this.elClose = null
    this.elHandler = null
    this.elButton = null
    this.displayMode = 'browser tab'
    this.deferredPrompt = null
    this.timeout = null

    this._initOptions(options)
    this._init()
  }

  // PLUGINS
  // -------------------------------------------------------------------------------------------------------------------
  // Initialisation des options
  _initOptions(options) {
    let tagOptions = this.el.dataset.options || null

    if (tagOptions) {
      try {
        tagOptions = decodeURIComponent(tagOptions)
      } catch (e) {
        if (this.debug) {
          console.debug(e)
        }
      }
    }

    try {
      tagOptions = JSON.parse(tagOptions)
    } catch (e) {
      if (this.debug) {
        console.debug(e)
      }
    }

    if (typeof tagOptions === 'object' && tagOptions !== null) {
      Object.assign(this.options, tagOptions)
    }

    Object.assign(this.options, options)
  }

  // Resolution d'objet depuis une clé à point
  _objResolver(dotKey, obj) {
    return dotKey.split('.').reduce(function (prev, curr) {
      return prev ? prev[curr] : null
    }, obj || self)
  }

  // Initialisation
  _init() {
    if (!('serviceWorker' in navigator)) {
      console.warn('[PwaInstaller] Service workers are not supported by this browser')
      return
    }

    this._initControls()
    this._initEvents()

    const mql = window.matchMedia('(display-mode: standalone)')

    if ('standalone' in navigator) {
      this.displayMode = 'standalone-ios'
    }

    if (mql.matches) {
      this.displayMode = 'standalone'
    }

    mql.addEventListener('change', e => {
      if (e.matches) {
        this.displayMode = 'standalone'
      }
    })

    if (this.debug) {
      console.debug('[PwaInstaller] fully initialized')
    }
  }

  // Initialisation
  _destroy() {
    this.initialized = true
  }

  // INITIALISATIONS
  // -------------------------------------------------------------------------------------------------------------------
  // Initialisation des éléments de contrôle.
  _initControls() {
    let $title = document.querySelector('[data-pwa-installer="title"]')
    if ($title) {
      $title.classList.add(this.option('classes.title'))
    }

    let $content = document.querySelector('[data-pwa-installer="content"]')
    if ($content) {
      $content.classList.add(this.option('classes.content'))
    }

    this.elClose = document.querySelector('[data-pwa-installer="close"]')
    if (this.elClose) {
      this.elClose.classList.add(this.option('classes.close'))
    }

    this.elButton = document.querySelector('[data-pwa-installer="button"]')
    if (this.elButton) {
      this.elButton.classList.add(this.option('classes.button'))
    }

    this.elHandler = document.querySelector('[data-pwa-installer="handler"]')
    if (this.elHandler) {
      this.elHandler.classList.add(this.option('classes.handler'))
      this.elHandler.style.display = 'none'
    }

    if (this.debug) {
      console.debug('[PwaInstaller] controls initialized')
    }
  }

  // Initialisation des événements déclenchement.
  _initEvents() {
    window.addEventListener('beforeinstallprompt', e => {
      e.preventDefault()

      this.elHandler.style.display = 'block'

      if (navigator.getInstalledRelatedApps) {
        navigator.getInstalledRelatedApps().then(apps => {
          if (apps.length === 0) {
            if (this.deferredPrompt === null) {
              this._setDisplayState()
            }
            this.deferredPrompt = e
          }
        })
      } else if (this.deferredPrompt === null) {
        this._setDisplayState()
        this.deferredPrompt = e
      } else {
        this.elHandler.style.display = 'none'
      }
    })

    window.addEventListener('appinstalled', () => {
      this.el.classList.toggle('show', false)
      this.elHandler.style.display = 'none'
      this._setDismissed()
    })

    if (this.elHandler) {
      this.elHandler.addEventListener('click', e => {
        e.preventDefault()

        if (this.el.classList.contains('show')) {
          this.el.classList.toggle('show', false)
        } else {
          this.el.classList.toggle('show', true)
        }
      })
    }

    if (this.elButton) {
      this.elButton.addEventListener('click', e => {
        e.preventDefault()

        if (this.deferredPrompt === undefined) {
          return
        }

        this.deferredPrompt.prompt()

        this.deferredPrompt.userChoice.then(choice => {
          if (choice.outcome === 'accepted') {
            window.deferredPrompt = null

            this.el.classList.toggle('show', false)
            this.elHandler.style.display = 'none'
            this._setDismissed()
          } else if (this.debug) {
            console.info('[PwaInstaller] User dismissed the install prompt')
          }
        })
      })
    }

    if (this.elClose) {
      this.elClose.addEventListener('click', e => {
        e.preventDefault()
        this._setDismissed()
        this.el.classList.toggle('show', false)
      })
    }

    this._setTimeout()
    if(this.timeout) {
      this.el.addEventListener('mouseenter', () => {
        this._clearTimeout()
      })

      this.el.addEventListener('mouseleave', () => {
        this._setTimeout()
      })
    }

    document.addEventListener('click', e => {
      let outside = true

      for (let node = e.target; node !== document.body; node = node.parentNode) {
        if (node.classList.contains('PwaInstaller')) {
          outside = false
        }
      }

      if (outside) {
        this.el.classList.toggle('show', false)
      }
    })

    if (this.debug) {
      console.debug('[PwaInstaller] events initialized')
    }
  }

  // EVENEMENTS
  // -----------------------------------------------------------------------------------------------------------------

  // ACTIONS
  // -----------------------------------------------------------------------------------------------------------------
  _clearDismissed() {
    localStorage.setItem('pwa-installer-dismiss', 'false')
  }

  _getDismissed() {
    return localStorage.getItem('pwa-installer-dismiss') === 'true'
  }

  _setDismissed() {
    localStorage.setItem('pwa-installer-dismiss', 'true')
  }

  _setDisplayState() {
    if (!this._getDismissed()) {
      this.el.classList.toggle('show', true)
    }
  }

  _setTimeout() {
      let number = this.option('timeout', 0)

      if (number !== 0) {
        this.timeout = setTimeout(() => {
          this.el.classList.toggle('show', false)
        }, this.option('timeout'))
      }
  }

  _clearTimeout() {
    clearTimeout(this.timeout)
    this.timeout = null
  }

  // ACCESSEURS
  // -------------------------------------------------------------------------------------------------------------------
  // Récupération d'options (syntaxe à point permise)
  option(key = null, defaults = null) {
    if (key === null) {
      return this.options
    }

    return this._objResolver(key, this.options) ?? defaults
  }
}

window.addEventListener('load', () => {
  const $elements = document.querySelectorAll('[data-observe="pwa-installer"]')

  if ($elements) {
    for (const $el of $elements) {
      new PwaInstaller($el)
    }
  }

  Observer('[data-observe="pwa-installer"]', function ($el) {
    new PwaInstaller($el)
  })
})

export default PwaInstaller