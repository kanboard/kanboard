LDAP Kimlik Doğrulaması
===================

Gereksinimler
------------

- PHP LDAP uzantısı etkinleştirin
- LDAP sunucusu:
	- OpenLDAP
	- Microsoft Active Directory
	- Novell eDirectory

İş Akışı
--------

LDAP kimlik doğrulaması etkinleştirildiğinde, oturum açma işlemi şu şekilde iş görür:

1. Önce veritabanını kullanarak kullanıcıyı doğrulamayı dener
2. Kullanıcı veritabanında bulunmazsa, bir LDAP kimlik doğrulaması yapılır
3. LDAP kimlik doğrulaması başarılı olursa, varsayılan olarak bir yerel kullanıcı otomatik olarak parola olmadan oluşturulur ve LDAP kullanıcıları olarak işaretlenir.

Tam ad ve e-posta adresi otomatik olarak LDAP sunucusundan alınır.

Kimlik Doğrulama Türleri
--------------------

| Tip               | Açıklama                                                               |
|-------------------|------------------------------------------------------------------------|
| Proxy Kullanıcısı | LDAP dizinine göz atmak için belirli bir kullanıcı kullanılır.         |
| Kullanıcı         | Son kullanıcı kimlik bilgileri LDAP dizinine göz atmak için kullanılır |
| Anonim            | LDAP tarama için kimlik doğrulama yapılmaz                             |

** Tavsiye edilen kimlik doğrulama yöntemi "Proxy"'dır**.

#### Anonim mod

```php
define('LDAP_BIND_TYPE', 'anonymous');
define('LDAP_USERNAME', null);
define('LDAP_PASSWORD', null);
```

Varsayılan değer budur, ancak bazı LDAP sunucuları güvenlik nedenleriyle anonim taramaya izin vermez.

#### Proxy mode

LDAP dizinine göz atmak için belirli bir kullanıcı kullanılır:

```php
define('LDAP_BIND_TYPE', 'proxy');
define('LDAP_USERNAME', 'my proxy user');
define('LDAP_PASSWORD', 'my proxy password');
```

#### Kullanıcı modu

Bu yöntem, son kullanıcı tarafından sağlanan kimlik bilgilerini kullanır.

Örneğin, Microsoft Active Directory varsayılan olarak anonim taramaya izin vermez ve bir proxy kullanıcısı kullanmak istemiyorsanız bu yöntemi kullanabilirsiniz.

```php
define('LDAP_BIND_TYPE', 'user');
define('LDAP_USERNAME', '%s@kanboard.local');
define('LDAP_PASSWORD', null);
```

Bu durumda, `LDAP_USERNAME` sabiti ldap kullanıcı adına bir örnek olarak kullanılır, örnekler:

- `%s@kanboard.local` Yerini alacak `my_user@kanboard.local`
- `KANBOARD\\%s` Yerini alacak `KANBOARD\my_user`

Kullanıcı LDAP filtresi
----------------

LDAP dizinindeki kullanıcıları bulmak için yapılandırma parametresi `LDAP_USER_FILTER` i kullanılır.

Örnekler:

- `(&(objectClass=user)(sAMAccountName=%s))` yerini alır `(&(objectClass=user)(sAMAccountName=my_username))`
- `uid=%s` yerini alır `uid=my_username`

[Active Directory için filtreler](http://social.technet.microsoft.com/wiki/contents/articles/5392.active-directory-ldap-syntax-filters.aspx) için diğer örnekler

Kanboard'a erişimi filtrelemek için örnek:

`(&(objectClass=user)(sAMAccountName=%s)(memberOf=CN=Kanboard Users,CN=Users,DC=kanboard,DC=local))`

Bu örnek, sadece "Kanboard Kullanıcıları" grubunun üyelerine Kanboard'a bağlanmalarına izin verir.

Microsoft Active Directory için Örnek
--------------------------------------

Diyelim ki 'KANBOARD' alanına (kanboard.local) sahibiz ve birincil denetleyicisi `myserver.kanboard.local` diyelim.

Proxy moduyla ilk örnek:

```php
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
```

Kullanıcı modu ile ikinci örnek:

```php
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
```

OpenLDAP için örnek
--------------------

LDAP sunucumuz `myserver.example.com` olup tüm kullanıcılar `ou=People,dc=example,dc=com` altına saklanır.

Bu örnek için, anonim bağlayıcı kullanıyoruz.

```php
<?php

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', true);

// LDAP server hostname
define('LDAP_SERVER', 'myserver.example.com');

// LDAP properties
define('LDAP_USER_BASE_DN', 'ou=People,dc=example,dc=com');
define('LDAP_USER_FILTER', 'uid=%s');
```

Otomatik hesap oluşturma işlemini devre dışı bırak
-----------------------------------

Varsayılan olarak, Kanboard hiçbir şey bulunamazsa otomatik olarak bir kullanıcı hesabı oluşturacaktır.

Kanboard'u yalnızca bazı kişilere kısıtlamak için kullanıcı hesapları oluşturmayı tercih ederseniz, bu davranışı devre dışı bırakabilirsiniz.

`LDAP_ACCOUNT_CREATION` değerini `false` olarak değiştirmeniz yeterlidir:

```php
// Automatically create user account
define('LDAP_ACCOUNT_CREATION', false);
```

Sorun giderme
---------------

### SELinux kısıtlamaları

SELinux etkinleştirilirse, Apache'nin LDAP sunucunuza erişmesine izin vermeniz gerekir.

- SELinux'ü izin moduna-permissive mode geçirebilir veya devre dışı bırakabilirsiniz (önerilmez).
- Örneğin, `setsebool -P httpd_can_network_connect=1` gibi tüm ağ bağlantılarına izin verebilir veya daha kısıtlayıcı bir kurala sahip olabilirsiniz

Her halükarda, resmi Redhat/Centos belgelerine bakın.

### Hata ayıklama modunu etkinleştir

Doğru şekilde LDAP kimlik doğrulamasını kuramazsanız, [hata ayıklama modunu etkinleştirebilirsiniz](config.markdown) ve günlük dosyalarını izleyebilirsiniz.

