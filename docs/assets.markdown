How to build assets (Javascript and CSS files)?
===============================================

Stylesheet and Javascript files are merged together and minified.

- Original CSS files are stored in the folder `assets/css/src/*.css`
- Original Javascript code is stored in the folder `assets/js/src/*.js`

To make a new build run this shell-script from a terminal:

```bash
./scripts/make-assets.sh
```

This script generates the files `assets/css/app.css` and `assets/js/app.js`.

This tool is only available in the repository (development version).
