Аутентификация LDAP
===================


Требования[¶](#requirements "Ссылка на этот заголовок")
-------------------------------------------------------



-   Включенное в PHP раширение LDAP



-   Сервер LDAP:



    -   OpenLDAP

    -   Microsoft Active Directory

    -   Novell eDirectory



Рабочий процесс[¶](#workflow "Ссылка на этот заголовок")
--------------------------------------------------------



Когда активирована аутентификация LDAP, процесс входа выглядит следующим образом:



1.  Выполняется попытка аутентификации пользователя в базе данных Канборда

2.  Если пользователь не найден в базе Канборда, выполняется аутентификация LDAP

3.  Если аутентификация LDAP выполнена успешно, по умолчанию, локальный пользователь (в Канборде) создается автоматически без пароля и помечается как пользователь LDAP.



Полное имя и email адрес автоматически подгружаются из сервера LDAP.



Типы аутентификации[¶](#authentication-types "Ссылка на этот заголовок")
------------------------------------------------------------------------


| Тип          | Описание                                                    |
|--------------|-------------------------------------------------------------|
| Proxy User   | Использовать специального пользователя для просмотра директории LDAP |
| User         | Использовать учетные данные конечного пользователя для просмотра директории LDAP |
| Anonymous    | Не надо выполнять аутентификацию для доступа к каталогу LDAP |


**Рекомендуемый метод аутентификации - “Proxy”**.



### Анонимный (Anonymous) метод[¶](#anonymous-mode "Ссылка на этот заголовок")



    define('LDAP_BIND_TYPE', 'anonymous');

    define('LDAP_USERNAME', null);

    define('LDAP_PASSWORD', null);



Этот метод используется по умолчанию, но некоторые сервера LDAP не поддерживают доступ анонимам, из соображений безопасности.



### Proxy метод[¶](#proxy-mode "Ссылка на этот заголовок")



Специальный пользователь используется для доступа к директории LDAP:



    define('LDAP_BIND_TYPE', 'proxy');

    define('LDAP_USERNAME', 'my proxy user');

    define('LDAP_PASSWORD', 'my proxy password');



### Пользовательский метод (user)[¶](#user-mode "Ссылка на этот заголовок")



Этот метод используется для доступа под учетной записью конечного пользователя.



Например, Microsoft Active Directory не разрешает подключение под анонимным пользователем и если вы не хотите использовать пользователя proxy, то используйте этот метод.



    define('LDAP_BIND_TYPE', 'user');

    define('LDAP_USERNAME', '%s@kanboard.local');

    define('LDAP_PASSWORD', null);



В этом методе, константа `LDAP_USERNAME` использутся как шаблон для пользователя ldap, например:



-   `%s@kanboard.local` будет заменен `my_user@kanboard.local`



-   `KANBOARD\\%s` будет заменен на `KANBOARD\my_user`



Фильтр пользователей LDAP[¶](#user-ldap-filter "Ссылка на этот заголовок")
--------------------------------------------------------------------------


Параметр конфигурации `LDAP_USER_FILTER` используется для поиска пользователей по директории LDAP.



Например:



-   `(&(objectClass=user)(sAMAccountName=%s))` будет заменено на `(&(objectClass=user)(sAMAccountName=указанный_пользователь))`


-   `uid=%s` is replaced by `uid=указанный_пользователь`



Другие примеры [фильтров для Active Directory](http://social.technet.microsoft.com/wiki/contents/articles/5392.active-directory-ldap-syntax-filters.aspx)



Пример фильра доступа в Канборд:



`(&(objectClass=user)(sAMAccountName=%s)(memberOf=CN=Kanboard Users,CN=Users,DC=kanboard,DC=local))`



Этот пример разрешает подключатся к Канборду только пользователям участникам группы “Kanboard Users”



Пример для Microsoft Active Directory[¶](#example-for-microsoft-active-directory "Ссылка на этот заголовок")
------------------------------------------------------------------------------------------------------------



Предположим, что мы имеем домен `KANBOARD` (kanboard.local) и контролер домена `myserver.kanboard.local`.



Первый пример для метода прокси (proxy):



    <?php



    // Enable LDAP authentication (false by default)

    define('LDAP_AUTH', true);



    define('LDAP_BIND_TYPE', 'proxy');

    define('LDAP_USERNAME', 'administrator@kanboard.local');

    define('LDAP_PASSWORD', 'my super secret password');



    // LDAP server hostname

    define('LDAP_SERVER', 'myserver.kanboard.local');



    // LDAP properties

    define('LDAP_USER_BASE_DN', 'CN=Users,DC=kanboard,DC=local');

    define('LDAP_USER_FILTER', '(&(objectClass=user)(sAMAccountName=%s))');



Второй пример с пользовательским методом (user):



    <?php



    // Enable LDAP authentication (false by default)

    define('LDAP_AUTH', true);



    define('LDAP_BIND_TYPE', 'user');

    define('LDAP_USERNAME', '%s@kanboard.local');

    define('LDAP_PASSWORD', null);



    // LDAP server hostname

    define('LDAP_SERVER', 'myserver.kanboard.local');



    // LDAP properties

    define('LDAP_USER_BASE_DN', 'CN=Users,DC=kanboard,DC=local');

    define('LDAP_USER_FILTER', '(&(objectClass=user)(sAMAccountName=%s))');



Пример для OpenLDAP[¶](#example-for-openldap "Ссылка на этот заголовок")
------------------------------------------------------------------------



Наш сервер LDAP - `myserver.example.com` и все пользователи хранятся в `ou=People,dc=example,dc=com`.



Для этого примера мы использовали анонимное подключение.



    <?php



    // Enable LDAP authentication (false by default)

    define('LDAP_AUTH', true);



    // LDAP server hostname

    define('LDAP_SERVER', 'myserver.example.com');



    // LDAP properties

    define('LDAP_USER_BASE_DN', 'ou=People,dc=example,dc=com');

    define('LDAP_USER_FILTER', 'uid=%s');



Выключение автоматического создания учетных записей[¶](#disable-automatic-account-creation "Ссылка на этот заголовок")
----------------------------------------------------------------------------------------------------------------------



По умолчанию, Канборд автоматически создает учетную запись пользователя, если такой пользователь не найден.



Вы можете выключить это поведение, если вы предпочитаете создавать учетные записи вручную.



Для этого установите значение `LDAP_ACCOUNT_CREATION` в `false`:



    // Automatically create user account

    define('LDAP_ACCOUNT_CREATION', false);



Устранение неисправностей[¶](#troubleshooting "Ссылка на этот заголовок")
-------------------------------------------------------------------------

Если включен SELinux, вы должны разрешить Apache доступ к вашему серверу LDAP.



-   Вы должны переключить SELinux в разрешающий режим (permissive mode) или совсем выключить (не рекомендуется)

-   Вы должны разрешить все сетевые подключения, например `setsebool -P httpd_can_network_connect=1` или назначить более ограничивающие правила



В любом случае, ознакомтесь с официальной документацией Redhat/Centos.



Если вам не удается настроить аутентификацию LDAP, то вы можете [включить режим отладки](config.markdown) и посмотреть файлы событий.






[Русская документация Kanboard](http://kanboard.ru/doc/)

