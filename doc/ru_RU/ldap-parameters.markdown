Параметры LDAP для конфигурации
===============================



Список доступных параметров LDAP:


| Параметр                  | Значение по умолчанию        |Описание                     |
|---------------------------|------------------------------|-----------------------------|
| `LDAP_AUTH`               | false                        | Включить аутентификацию LDAP    |
| `LDAP_SERVER`             | Нет значения                 | Имя сервера LDAP                |
| `LDAP_PORT`               | 389                          | Порт сервера LDAP               |
| `LDAP_SSL_VERIFY`         | true                         | Проверка сертификата для URL `ldaps://`  |
| `LDAP_START_TLS`          | false                        | Включение LDAP start TLS                 |
| `LDAP_USERNAME_CASE_SENSITIVE`  | false                  | Включение/выключение нижнего и верхнего регистра букв в Канборд для пользователей ldap для исключения дублирования пользователей (база данных чувствительна к регистру)       |
| `LDAP_BIND_TYPE`  | anonymous                    | Тип подключения: “anonymous”, “user” or “proxy”                 |
| `LDAP_USERNAME`   | null                         | Имя пользователя LDAP для использования в методе proxy или  шаблон имени пользователя для использования в методе user       |
| `LDAP_PASSWORD`   | null                         | Пароль LDAP при использовании метода proxy                      |
| `LDAP_USER_BASE_DN`| Нет значения                 | Уникальное имя (DN) LDAP для пользователей (Пример: “CN=Users,DC=kanboard,DC=local”)     |
| `LDAP_USER_FILTER`       | Нет значения                 | Шаблон LDAP, который используется для поиска пользователей      (Пример: “(&(objectClass=user)(sAMAccountName=%s))”)            |
| `LDAP_USER_ATTRIBUTE_USERNAME`    | uid                          | Атрибут LDAP для имени пользователя (Например: “samaccountname”)                                               |
| `LDAP_USER_ATTRIBUTE_FULLNAME`  | cn                           | Атрибут LDAP полного имени пользователя (Например: “displayname”)                                                  |
| `LDAP_USER_ATTRIBUTE_EMAIL`   | mail                         | Атрибут LDAP для email пользователя                             |
| `LDAP_USER_ATTRIBUTE_GROUPS`       | memberof                     | Атрибут LDAP для поиска групп в профиле пользователя            |
| `LDAP_USER_ATTRIBUTE_PHOTO`           | Нет значения                 | Атрибут LDAP для поиска фотографии пользователя (jpegPhoto или thumbnailPhoto)                                                 |
| `LDAP_USER_ATTRIBUTE_LANGUAGE` | Нет значения                 | Атрибут LDAP для языка пользователя (preferredlanguage), применимый формат языка - “ru-RU”                               |
| `LDAP_USER_CREATION`     | true                         | Включение автоматического создания пользователя из LDAP    |
| `LDAP_GROUP_ADMIN_DN`   | Нет значения                 | Уникальное имя (DN) LDAP для администраторов (Например: “CN=Kanboard-Admins,CN=Users,DC=kanboard,DC=local”)             |
| `LDAP_GROUP_MANAGER_DN`     | Нет значения                 | Уникальное имя (DN) LDAP для менеджеров (Например: “CN=Kanboard Managers,CN=Users,DC=kanboard,DC=local”)                        |
| `LDAP_GROUP_PROVIDER`       | false                        | Включение поставщика групп LDAP для “Разрешения” в проектах     |
| `LDAP_GROUP_BASE_DN` | Нет значения                 | Уникальное имя (Base DN) LDAP для групп                         |
| `LDAP_GROUP_FILTER`                 | Нет значения                 | Фильтр групп LDAP (Например: “(&(objectClass=group)(sAMAccountName=%s\*))”) |
| `LDAP_GROUP_USER_FILTER` | Empty | Если определено, то Канборд будет искать группы пользователей в LDAP\_GROUP\_BASE\_DN с помощью этого фильтра, это удобно только для posixGroups (Например: `(&(objectClass=posixGroup)(memberUid=%s))`| 
| `LDAP_GROUP_ATTRIBUTE_NAME`  | cn | атрибут LDAP для имени группы |


Примечание



-   Атрибуты LDAP должны быть в нижнем регистре





[Русская документация Kanboard](http://kanboard.ru/doc/)

