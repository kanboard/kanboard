LDAP Grubu Senkronizasyonu
==========================

Gereksinimler
------------

- LDAP kimlik doğrulamasının düzgün şekilde yapılandırılmış olmasını sağlayın
- `memberOf` veya `memberUid` (PosixGroups) destekleyen bir LDAP sunucusu kullanın

Kullanıcı gruplarını LDAP gruplarına dayalı olarak otomatik olarak tanımlama
----------------------------------------------------

Bu sabitleri yapılandırma dosyanızda kullanın:

- `LDAP_GROUP_ADMIN_DN`: Uygulama yöneticileri için ayırt edici isimler
- `LDAP_GROUP_MANAGER_DN`: Uygulama yöneticileri için ayırt edici isimler

### Active Directory Örneği:

```php
define('LDAP_GROUP_ADMIN_DN', 'CN=Kanboard Admins,CN=Users,DC=kanboard,DC=local');
define('LDAP_GROUP_MANAGER_DN', 'CN=Kanboard Managers,CN=Users,DC=kanboard,DC=local');
```

- "Kanboard Admins" kişileri "Yönetici" rolüne sahip olacak
- "Kanboard Yöneticilerinin" üyeleri "Yönetici" rolüne sahip olacak
- Herkesin "Kullanıcı" rolü olacak

### Posix Gruplu OpenLDAP Örneği:

```php
define('LDAP_GROUP_BASE_DN', 'ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_USER_FILTER', '(&(objectClass=posixGroup)(memberUid=%s))');
define('LDAP_GROUP_ADMIN_DN', 'cn=Kanboard Admins,ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_MANAGER_DN', 'cn=Kanboard Managers,ou=Groups,dc=kanboard,dc=local');
```

LDAP sunucunuz `memberOf` yerine `memberUid` kullanıyorsa `LDAP_GROUP_USER_FILTER` **parametresini tanımlamanız gerekir**.
Bu örnekteki tüm parametreler zorunludur.

Proje izinleri için LDAP gruplarını otomatik olarak yükle
------------------------------------------------------

Bu özellik, LDAP gruplarını otomatik olarak Kanboard gruplarıyla senkronize etmenizi sağlar.
Her gruba farklı bir proje rolü atanabilir.

Proje izinleri sayfasında, kullanıcılar otomatik tamamlama alanına gruplar girebilir ve Kanboard herhangi bir sağlayıcı etkin grupları arayabilir.

Grup yerel veritabanında yoksa, otomatik olarak senkronize edilecektir.

- `LDAP_GROUP_PROVIDER`: LDAP grup sağlayıcısını etkinleştirin
- `LDAP_GROUP_BASE_DN`: LDAP dizinindeki grupları bulmak için ayırt edici adlar
- `LDAP_GROUP_FILTER`: Sorguyu gerçekleştirmek için kullanılan LDAP filtresi
- `LDAP_GROUP_ATTRIBUTE_NAME`: grup adını almak için kullanılan LDAP özelliği

### Active Directory Örneği:

```php
define('LDAP_GROUP_PROVIDER', true);
define('LDAP_GROUP_BASE_DN', 'CN=Groups,DC=kanboard,DC=local');
define('LDAP_GROUP_FILTER', '(&(objectClass=group)(sAMAccountName=%s*))');
```

Yukarıdaki örnekte verilen filtreyle Kanboard, sorguyla eşleşen grupları arayacaktır.
Son kullanıcı, otomatik tamamlama kutusuna "Grupum" metnini girerse, Kanboard, desenle eşleşen tüm grupları döndürür: `(&(objectClass=group)(sAMAccountName=My group*))`.

- Not 1: Burada `*` özel karakterleri önemlidir, 00aksi halde tam bir eşleşme yapılacak**.
- Not 2: Bu özellik yalnızca "proxy" veya "anonim" modda yapılandırılmış LDAP kimlik doğrulamasıyla uyumludur

[Active Directory için daha fazla LDAP filtresi örneği](http://social.technet.microsoft.com/wiki/contents/articles/5392.active-directory-ldap-syntax-filters.aspx)

### Example for OpenLDAP with Posix Groups:

```php
define('LDAP_GROUP_PROVIDER', true);
define('LDAP_GROUP_BASE_DN', 'ou=Groups,dc=kanboard,dc=local');
define('LDAP_GROUP_FILTER', '(&(objectClass=posixGroup)(cn=%s*))');
```

