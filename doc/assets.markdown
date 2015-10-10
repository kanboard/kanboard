How to build assets (Javascript and CSS files)
==============================================

Stylesheet and Javascript files are merged together and minified.

- Original CSS files are stored in the folder `assets/css/src/*.css`
- Original Javascript code is stored in the folder `assets/js/src/*.js`

Requirements
------------

- Unix operating system
- make
- yuicompressor in your path (`brew install yuicompressor`)

Build assets
------------

- Build Stylesheet files: `make css`
- Build Javascript files: `make js`
- Build both: `make`

This script generates the files `assets/css/app.css` and `assets/js/app.js`.

This tool is only available in the repository (development version).
