'use strict'

let reloadButtons = document.getElementsByClassName('PwaOffline-button--reload')

if (reloadButtons.length) {
  Array.from(reloadButtons).forEach((button) => {
    button.addEventListener('click', () => {
      window.location.reload()
    })
  })
}