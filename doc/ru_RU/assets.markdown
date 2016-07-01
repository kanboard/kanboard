Как создать asset (Javascript и CSS файлы)[¶](#how-to-build-assets-javascript-and-css-files "Ссылка на этот заголовок")

=======================================================================================================================



Файлы CSS стилей и Javascript объединены вместе и минимизированы.



-   Оригинальные файлы CSS хранятся в каталоге `assets/css/src/*.css`{.docutils .literal}



-   Оригинальные файлы Javascript хранятся в каталоге `assets/js/src/*.js`{.docutils .literal}



-   `assets/*/vendor.min.*`{.docutils .literal} - внешние зависимости объединены и минимизированы



-   `assets/*/app.min.*`{.docutils .literal} - исходный код приложения объединены и минимизированы



Требования[¶](#requirements "Ссылка на этот заголовок")

-------------------------------------------------------



-   [NodeJS](https://nodejs.org/) с `npm`{.docutils .literal}



Сборка файлов Javascript и CSS[¶](#building-javascript-and-css-files "Ссылка на этот заголовок")

------------------------------------------------------------------------------------------------



Канборд использует [Gulp](http://gulpjs.com/) для сборки asset и [Bower](http://bower.io/) для управления зависимостями. Эти утилиты устанавлены в проекте как зависимости NodeJS.



### Запустить все[¶](#run-everything "Ссылка на этот заголовок")



    make static



### Собрать `vendor.min.js`{.docutils .literal} и `vendor.min.css`{.docutils .literal}[¶](#build-vendor-min-js-and-vendor-min-css "Ссылка на этот заголовок")



    gulp vendor



### Собрать `app.min.js`{.docutils .literal}[¶](#build-app-min-js "Ссылка на этот заголовок")



    gulp js



### Собрать `app.min.css`{.docutils .literal}[¶](#build-app-min-css "Ссылка на этот заголовок")



    gulp css



Примечание[¶](#notes "Ссылка на этот заголовок")

------------------------------------------------



Сборка asset невозможна из архива Kanboard, вы должны клонировать репозиторий.



### [Оглавление](index.markdown)



-   [Как создать asset (Javascript и CSS файлы)](#)

    -   [Требования](#requirements)

    -   [Сборка файлов Javascript и CSS](#building-javascript-and-css-files)

        -   [Запустить все](#run-everything)

        -   [Собрать `vendor.min.js`{.docutils .literal} и `vendor.min.css`{.docutils .literal}](#build-vendor-min-js-and-vendor-min-css)

        -   [Собрать `app.min.js`{.docutils .literal}](#build-app-min-js)

        -   [Собрать `app.min.css`{.docutils .literal}](#build-app-min-css)

    -   [Примечание](#notes)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/assets.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/assets.txt)

