Cómo construir assets (Javascript and CSS files)
==============================================

Stylesheet y Javascript se combinan entre sí y minifican..

- Los archivos originales CSS estan almacenados en la carpeta `assets/css/src/*.css`
- Los codigos originales estan almacenados en la carpeta `assets/js/src/*.js`
- `assets/*/vendor.min.*`son dependencias externas que se fusionaron y minificaron
- `assets/*/app.min.*` son el código fuente de la aplicación que se fusionaron y  minificaron

Requirimientos
------------

- [NodeJS](https://nodejs.org/) con `npm`

Construir archivos Javascript y CSS
-----------------------------------

Kanboard usa [Gulp](http://gulpjs.com/) para construir los assets y [Bower](http://bower.io/) para manejar las dependencias.
Estas herramientas se instalan con node.js como dependencias en el proyecto.

### Ejecutar todo

```bash
make static
```

### Build `vendor.min.js` y `vendor.min.css`

```bash
gulp vendor
```

### Build `app.min.js`

```bash
gulp js
```

### Build `app.min.css`

```bash
gulp css
```

Notas
-----

La construcción de los assets no es posible desde el archivo de Kanboard, tu tiene que clonar el repositorio.
