Фотография пользователя из профиля LDAP[¶](#ldap-user-profile-photo "Ссылка на этот заголовок")

===============================================================================================



Канборд может автоматически загружать фотографию пользователя из сервера LDAP.



Эта функция возможна только если активирована аутентификация LDAP и указан параметр `LDAP_USER_ATTRIBUTE_PHOTO`{.docutils .literal}.



Настройка[¶](#configuration "Ссылка на этот заголовок")

-------------------------------------------------------



В вашем `config.php`{.docutils .literal}, вы должны установить атрибут LDAP, используемый для хранения изображения.



    define('LDAP_USER_ATTRIBUTE_PHOTO', 'jpegPhoto');



Обычно используются атрибуты `jpegPhoto`{.docutils .literal} или `thumbnailPhoto`{.docutils .literal}. Изображения могут хранится в формате JPEG или PNG.



Для загрузки изображения в пользовательски профиль, администраторы Active Directory могут использовать программу [AD Photo Edit](http://www.cjwdev.co.uk/Software/ADPhotoEdit/Info.html).



Примечание[¶](#notes "Ссылка на этот заголовок")

------------------------------------------------



Изображение из профиля **загружается при входе, только если изображение не было загружено ранее**.



Для смены изображения, нужно вручную удалить ранее загруженное изображение из профиля пользователя.



### [Оглавление](index.markdown)



-   [Фотография пользователя из профиля LDAP](#)

    -   [Настройка](#configuration)

    -   [Примечание](#notes)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/ldap-profile-picture.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/ldap-profile-picture.txt)

