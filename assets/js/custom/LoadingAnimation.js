/* eslint-env jquery */

// eslint-disable-next-line no-unused-vars
const LoadingAnimation = function (color, loaderText) {
  color = typeof color !== 'undefined' ? color : '#177f8d'
  const self = this

  self.spinnerLayer = $('<div id="loader"></div>')
  self.spinner = $('<div class="loader"></div>')
  self.spinner.append($('<div class="loader-inner"></div>'))
  self.spinnerLayer.append(self.spinner)
  self.spinnerLayer.append($('<div class="loader-text">' + loaderText + '</div>'))
  self.spinnerLayer.appendTo($('body'))
  $('.loader-inner').css('background-color', color)
  self.spinnerLayer.hide()

  self.show = function () {
    self.spinnerLayer.show()
  }

  self.hide = function () {
    self.spinnerLayer.hide()
  }
}
