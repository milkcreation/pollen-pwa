/**
 * @see https://github.com/joemottershaw/vanilla-js-plugin-boilerplate/blob/master/boilerplate.js
 */
;(function (root, factory) {
  if (typeof exports === 'object' && exports) {
    module.exports = factory();
  } else if (typeof define === 'function' && define.amd) {
      define([], factory());
  }
}((window || module || {}), function() {
  'use strict';

  const plugin = {};

  const defaults = {
    property: 'Initialized',
    l10n: {},
    callbackInitializeBefore: () => {
      //console.log('callbackInitializeBefore')
    },
    callbackInitializeAfter: () => {
      //console.log('callbackInitializeAfter')
    },
    callbackRefreshBefore: () => {
      //console.log('callbackRefreshBefore')
    },
    callbackRefreshAfter: () => {
      //console.log('callbackRefreshAfter')
    },
    callbackDestroyBefore: () => {
      //console.log('callbackDestroyBefore')
    },
    callbackDestroyAfter: () => {
      //console.log('callbackDestroyAfter')
    }
  }

  const defaultsL10n = {
    button: {
      default: 'Activer/Désactiver',
      enabled: 'Désactiver',
      disabled: 'Activer',
    },
    process: {
      computing:'Chargement...',
      incompatible: 'Indisponible depuis ce navigateur',
      enabling: 'Veuillez d\'abord activer les notifications !'
    },
  }

  const applicationServerKey = 'BMBlr6YznhYMX3NgcWIDRxZXs0sh7tCv7_YCsWcww0ZCv9WGg-tRCXfMEHTiBPCksSqeve1twlbmVAZFv7GSuj0'

  let isPushEnabled = false;

  /**
   * Constructor.
   * @param  {element}  element  The selector element(s).
   * @param  {object}   options  The plugin options.
   * @return {void}
   */
  function Plugin(element, options) {
    plugin.this = this;
    plugin.element = element;
    plugin.defaults = defaults;
    plugin.options = options;
    plugin.settings = Object.assign({}, defaults, options);
    plugin.l10n = Object.assign({}, defaultsL10n, plugin.settings.l10n || {});

    plugin.el = document.querySelector(plugin.element)
    if (plugin.el) {
      plugin.el.addEventListener('click', function () {
        if (isPushEnabled) {
          pushUnsubscribe()
        } else {
          pushSubscribe()
        }
      })
    }

    if (!('serviceWorker' in navigator)) {
      console.warn('Service workers are not supported by this browser')
      changePushButtonState('incompatible')
      return;
    }

    if (!('PushManager' in window)) {
      console.warn('Push notifications are not supported by this browser')
      changePushButtonState('incompatible')
      return;
    }

    if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
      console.warn('Notifications are not supported by this browser')
      changePushButtonState('incompatible')
      return;
    }

    if (Notification.permission === 'denied') {
      console.warn('Notifications are denied by the user');
      changePushButtonState('incompatible');
      return;
    }

    navigator.serviceWorker.ready.then(
        () => {
          console.log('[SW] Service worker has been registered');
          pushSubscriptionUpdate();
        },
        e => {
          console.error('[SW] Service worker registration failed', e);
          changePushButtonState('incompatible');
        }
    )

    // Initialize the plugin
    plugin.this.initialize()
  }

  const changePushButtonState = state => {
    switch (state) {
      case 'enabled':
        plugin.el.disabled = false
        plugin.el.textContent = plugin.l10n.button.enabled
        isPushEnabled = true
        break
      case 'disabled':
        plugin.el.disabled = false
        plugin.el.textContent = plugin.l10n.button.disabled
        isPushEnabled = false
        break
      case 'computing':
        plugin.el.disabled = true
        plugin.el.textContent = plugin.l10n.process.computing
        break
      case 'incompatible':
        plugin.el.disabled = true;
        plugin.el.textContent = plugin.l10n.process.incompatible
        break
      default:
        console.error('Unhandled push button state', state)
        break
    }
  }

  const urlBase64ToUint8Array = base64String => {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/')

    const rawData = window.atob(base64)
    const outputArray = new Uint8Array(rawData.length)

    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i)
    }
    return outputArray
  }

  const checkNotificationPermission = () => {
    return new Promise((resolve, reject) => {
      if (Notification.permission === 'denied') {
        return reject(new Error('Push messages are blocked.'));
      }
      if (Notification.permission === 'granted') {
        return resolve();
      }
      if (Notification.permission === 'default') {
        return Notification.requestPermission().then(result => {
          if (result !== 'granted') {
            reject(new Error('Bad permission result'));
          } else {
            resolve();
          }
        });
      }
      return reject(new Error('Unknown permission'));
    });
  }

  const pushSubscribe = () => {
    changePushButtonState('computing');

    return checkNotificationPermission()
        .then(() => navigator.serviceWorker.ready)
        .then(serviceWorkerRegistration =>
            serviceWorkerRegistration.pushManager.subscribe({
              userVisibleOnly: true,
              applicationServerKey: urlBase64ToUint8Array(applicationServerKey),
            })
        )
        .then(subscription => {
          // Subscription was successful
          // create subscription on your server
          return pushSubscriptionXhr(subscription, 'POST');
        })
        .then(subscription => subscription && changePushButtonState('enabled')) // update your UI
        .catch(e => {
          if (Notification.permission === 'denied') {
            // The user denied the notification permission which
            // means we failed to subscribe and the user will need
            // to manually change the notification permission to
            // subscribe to push messages
            console.warn('Notifications are denied by the user.');
            changePushButtonState('incompatible');
          } else {
            // A problem occurred with the subscription; common reasons
            // include network errors or the user skipped the permission
            console.error('Impossible to subscribe to push notifications', e);
            changePushButtonState('disabled');
          }
        });
  }

  const pushUnsubscribe = () => {
    changePushButtonState('computing');

    // To unsubscribe from push messaging, you need to get the subscription object
    navigator.serviceWorker.ready
        .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
        .then(subscription => {
          // Check that we have a subscription to unsubscribe
          if (!subscription) {
            // No subscription object, so set the state
            // to allow the user to subscribe to push
            changePushButtonState('disabled');
            return;
          }

          // We have a subscription, unsubscribe
          // Remove push subscription from server
          return pushSubscriptionXhr(subscription, 'DELETE');
        })
        .then(subscription => subscription.unsubscribe())
        .then(() => changePushButtonState('disabled'))
        .catch(e => {
          // We failed to unsubscribe, this can lead to
          // an unusual state, so  it may be best to remove
          // the users data from your data store and
          // inform the user that you have done so
          console.error('Error when unsubscribing the user', e);
          changePushButtonState('disabled');
        });
  }

  const pushSubscriptionUpdate = () => {
    navigator.serviceWorker.ready
        .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
        .then(subscription => {
          changePushButtonState('disabled');

          if (!subscription) {
            // We aren't subscribed to push, so set UI to allow the user to enable push
            return;
          }
          // Keep your server in sync with the latest endpoint
          return pushSubscriptionXhr(subscription, 'PUT');
        })
        .then(subscription => subscription && changePushButtonState('enabled')) // Set your UI to show they have subscribed for push messages
        .catch(e => {
          console.error('Error when updating the subscription', e);
        });
  }

  const pushSubscriptionXhr = async (subscription, method) => {
    const key = subscription.getKey('p256dh');
    const token = subscription.getKey('auth');
    const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

    try {
      let response = await fetch('push-test-subscription', {
        method: method,
        headers: {
          'Content-type': 'application/json; charset=UTF-8',
          'X-Requested-with': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          endpoint: subscription.endpoint,
          publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
          authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
          contentEncoding: contentEncoding
        })
      })

      if (response.ok) {
        let data = await response.json()
        console.log(data)
      } else {
        console.log(response.status)
      }
      return subscription
    } catch (e) {
      console.log(e)
    }
  }

  const pushSendXhr = async subscription => {
    const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];
    const jsonSubscription = subscription.toJSON();

    try {
      let response = await fetch('push-test-send', {
        method: 'POST',
        headers: {
          'Content-type': 'application/json; charset=UTF-8',
          'X-Requested-with': 'XMLHttpRequest'
        },
        body: JSON.stringify(Object.assign(jsonSubscription, {contentEncoding}))
      })

      if (response.ok) {
        let data = await response.json()
        console.log(data)
      } else {
        console.log(response.status)
      }
      return subscription
    } catch (e) {
      console.log(e)
    }
  }

  /**
   * Public variables and methods.
   * @type {object}
   */
  Plugin.prototype = {
    /**
     * Initialize the plugin.
     * @param  {bool}  silent  Suppress callbacks.
     * @return {void}
     */
    initialize: (silent = false) => {
      // Destroy the existing initialization silently
      plugin.this.destroySilently();

      // Check if the callbacks should not be suppressed
      if (!silent) {
        // Call the initialize before callback
        plugin.settings.callbackInitializeBefore.call();
      }

      // Check if the callbacks should not be suppressed
      if (!silent) {
        // Call the initialize after callback
        plugin.settings.callbackInitializeAfter.call();
      }
    },

    /**
     * An example of a public method.
     * @return {void}
     */
    publicMethod: () => {
      // Your public method code here...
    },

    getName: () => {
      return plugin.name
    },

    /**
     * Refresh the plugins initialization.
     * @param  {bool}  silent  Suppress callbacks.
     * @return {void}
     */
    refresh: (silent = false) => {
      // Check if the callbacks should not be suppressed
      if (!silent) {
        // Call the refresh before callback
        plugin.settings.callbackRefreshBefore.call();
      }

      // Destroy the existing initialization
      plugin.this.destroy(silent);

      // Initialize the plugin
      plugin.this.initialize(silent);

      // Check if the callbacks should not be suppressed
      if (!silent) {
        // Call the refresh after callback
        plugin.settings.callbackRefreshAfter.call();
      }
    },

    /**
     * Destroy an existing initialization.
     * @param  {bool}  silent  Suppress callbacks.
     * @return {void}
     */
    destroy: (silent = false) => {
      // Check if the callbacks should not be suppressed
      if (!silent) {
        // Call the destroy before callback
        plugin.settings.callbackDestroyBefore.call();
      }

      // Remove anything set by the initialization method here...

      // Check if the callbacks should not be suppressed
      if (!silent) {
        // Call the destroy after callback
        plugin.settings.callbackDestroyAfter.call();
      }
    },

    /**
     * Call the refresh method silently.
     * @return {void}
     */
    refreshSilently: () => {
      // Call the refresh method silently
      plugin.this.refresh(true);
    },

    /**
     * Call the destroy method silently.
     * @return {void}
     */
    destroySilently: () => {
      // Call the destroy method silently
      plugin.this.destroy(true);
    }
  };
  // Return the plugin
  return Plugin;
}));