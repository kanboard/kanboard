Синхронизация групп LDAP
========================



Требования[¶](#requirements "Ссылка на этот заголовок")
-------------------------------------------------------



-   Правильно настроенную аутентификацию LDAP



-   Используется сервер LDAP, который поддерживает `memberOf` или `memberUid` (PosixGroups)



Автоматическое определение ролей пользователей на основании LDAP групп[¶](#define-automatically-user-roles-based-on-ldap-groups "Ссылка на этот заголовок")
-----------------------------------------------------------------------------------------------------------------------------------------------------------



Используйте следующие константы в вашем конфигурационном файле:



-   `LDAP_GROUP_ADMIN_DN`: Уникальные имена (Distinguished Names) для администраторов приложения



-   `LDAP_GROUP_MANAGER_DN`: Уникальные имена (Distinguished Names) для менеджеров приложения



### Пример для Active Directory:[¶](#example-for-active-directory "Ссылка на этот заголовок")



    define('LDAP_GROUP_ADMIN_DN', 'CN=Kanboard Admins,CN=Users,DC=kanboard,DC=local');

    define('LDAP_GROUP_MANAGER_DN', 'CN=Kanboard Managers,CN=Users,DC=kanboard,DC=local');



-   Участники группы “Kanboard Admins” будут иметь роль “Администратор”



-   Участники группы “Kanboard Managers” будут иметь роль “Менеджер”



-   Все, кто не попадает под предыдущие определения, будут иметь роль “Пользователь”



### Пример OpenLDAP с Posix Groups:[¶](#example-for-openldap-with-posix-groups "Ссылка на этот заголовок")



    define('LDAP_GROUP_BASE_DN', 'ou=Groups,dc=kanboard,dc=local');

    define('LDAP_GROUP_USER_FILTER', '(&(objectClass=posixGroup)(memberUid=%s))');

    define('LDAP_GROUP_ADMIN_DN', 'cn=Kanboard Admins,ou=Groups,dc=kanboard,dc=local');

    define('LDAP_GROUP_MANAGER_DN', 'cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local');



Вы **должны определить параметр** `LDAP_GROUP_USER_FILTER`, если ваше сервер LDAP использует `memberUid` вместо `memberOf`. Все параметры в этом примере обязательные.



Автоматическая загрузка групп LDAP для Канборд проекта[¶](#automatically-load-ldap-groups-for-project-permissions "Ссылка на этот заголовок")
---------------------------------------------------------------------------------------------------------------------------------------------



Эта возможность позволяет вам синхронизировать автоматически группы LDAP с группами Канборд. Каждая группа может иметь разные роли в проектах.



В проекте на странице *Разрешения*, можно ввести имя группы (имеется автодополнение) и Канборд будет искать группу во всех подключенных поставщиках.



Если группа не найдена в локальной базе данных, то она будет автоматически синхронизированна.



-   `LDAP_GROUP_PROVIDER`: Включение поставщика группы LDAP



-   `LDAP_GROUP_BASE_DN`: Уникальное имя (Distinguished Names) для поиска группы в LDAP директории



-   `LDAP_GROUP_FILTER`: фильтр LDAP используемый для выполнения запроса



-   `LDAP_GROUP_ATTRIBUTE_NAME`: атрибут LDAP используемый для получения имени группы



### Пример для Active Directory:[¶](#id1 "Ссылка на этот заголовок")



    define('LDAP_GROUP_PROVIDER', true);

    define('LDAP_GROUP_BASE_DN', 'CN=Groups,DC=kanboard,DC=local');

    define('LDAP_GROUP_FILTER', '(&(objectClass=group)(sAMAccountName=%s*))');



С помощью фильтра, в примере выше, Канборд будет искать группы соответсвующие запросу. Если пользователь введет текст “Мои группы” в автозаполняемое поле, Канборд вернет все группы которые соответсвуют шаблону: `(&(objectClass=group)(sAMAccountName=Мои группы*))`.



-   Примечание 1: Спец символ `*` очень важен, в противном случает **будет выбрано только точное совпадение**



-   Примечание 2: Эта функция возможна только с аутентификацией LDAP настроенной на метод “proxy” или “anonymous”



[Больше примеров фильтров LDAP для Active Directory](http://social.technet.microsoft.com/wiki/contents/articles/5392.active-directory-ldap-syntax-filters.aspx)



### Пример OpenLDAP с Posix Groups:[¶](#id2 "Ссылка на этот заголовок")



    define('LDAP_GROUP_PROVIDER', true);

    define('LDAP_GROUP_BASE_DN', 'ou=Groups,dc=kanboard,dc=local');

    define('LDAP_GROUP_FILTER', '(&(objectClass=posixGroup)(cn=%s*))');






[Русская документация Kanboard](http://kanboard.ru/doc/)

