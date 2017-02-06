Varlıklarğ-assets (Javascript ve CSS dosyaları) nasıl oluşturulur 
==============================================

Stil sayfası ve Javascript dosyaları bir araya getirilir ve küçültülür.

- Orijinal CSS dosyaları `assets/css/src/*.css` klasöründe saklanır
- Orijinal Javascript kodu `assets/js/src/*.js` klasöründe saklanır
- `assets/*/vendor.min.*` birleştirilmiş ve küçültülmüş harici bağımlılıklardır
- `assets/*/app.min.*` birleştirme ve küçültülmüş uygulama kaynak kodu

Gereksinimler
------------

- `npm` ile [NodeJS](https://nodejs.org/) 

Javascript ve CSS dosyalarını oluşturma
---------------------------------

Kanboard, öğeleri oluşturmak için [Gulp](http://gulpjs.com/) ve bağımlılıkları yönetmek için [Bower](http://bower.io/) kullanır.
Bu araçlar, projeye NodeJS bağımlılıkları olarak yüklenir.

### Her şeyi çalıştır

```bash
make static
```

### `vendor.min.js` ve `vendor.min.css` leri oluşturun

```bash
gulp vendor
```

### `app.min.js` oluşturun

```bash
gulp js
```

### `app.min.css` oluşturun

```bash
gulp css
```

Notlar
-----

Varlıkların oluşturulması Kanboard'un arşivinden mümkün değildir, havuzun klonlamanız gerekir.

