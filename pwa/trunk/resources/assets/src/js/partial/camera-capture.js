/* global navigator */
'use strict'

function htmlToElement(html) {
  let template = document.createElement('template')
  html = html.trim()
  template.innerHTML = html

  return template.content.firstChild
}

function getUserMedia(options, successCallback, failureCallback) {
  let api = navigator.getUserMedia || navigator.webkitGetUserMedia ||
      navigator.mozGetUserMedia || navigator.msGetUserMedia
  if (api) {
    return api.bind(navigator)(options, successCallback, failureCallback)
  }
}

let theStream

function getStream() {
  if (!navigator.mediaDevices && !navigator.getUserMedia && !navigator.webkitGetUserMedia &&
      !navigator.mozGetUserMedia && !navigator.msGetUserMedia) {
    console.log('User Media API not supported.')
    return
  }

  let constraints = {video: {facingMode: {exact : "environment" }, width: 720, height: 1280}}

  getUserMedia(constraints, function (stream) {
    let mediaControl = document.querySelector('video')

    if ('srcObject' in mediaControl) {
      mediaControl.srcObject = stream
    } else if (navigator.mozGetUserMedia) {
      mediaControl.mozSrcObject = stream
    } else {
      mediaControl.src = (window.URL || window.webkitURL).createObjectURL(stream)
    }

    theStream = stream
  }, function (err) {
    console.log('Error: ' + err)
  })
}

function takePhoto() {
  if (!('ImageCapture' in window)) {
    console.log('ImageCapture is not available')
    return
  }

  if (!theStream) {
    console.log('Grab the video stream first!')
    return
  }

  let theImageCapturer = new ImageCapture(theStream.getVideoTracks()[0])

  theImageCapturer.takePhoto()
      .then(blob => {
        let ul = document.getElementsByClassName('PwaCameraCapture-photos'),
            li = document.createElement('li'),
            item = document.createElement('div'),
            img = document.createElement('img')

        li.classList.add('PwaCameraCapture-photo')
        item.classList.add('PwaCameraCapture-photoItem')
        img.classList.add('PwaCameraCapture-photoItemImg')

        console.log(blob)

        img.src = URL.createObjectURL(blob)
        item.appendChild(img)
        li.appendChild(item)

        Array.from(ul).forEach((el) => {
          el.appendChild(li)
        })
      })
      .catch(err => alert('Error: ' + err))
}

getStream()

document.getElementById('takePhoto').addEventListener('click', ((e) => takePhoto()))