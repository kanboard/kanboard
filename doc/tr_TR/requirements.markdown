Gereksinimler
============

Sunucu tarafı
-----------

### Uyumlu İşletim Sistemleri

| İşletim sistemi                    |
|--------------------------------------|
| Linux Ubuntu Xenial Xerus 16.04 LTS  |
| Linux Ubuntu Trusty 14.04 LTS        |
| Linux Centos 6.x                     |
| Linux Centos 7.x                     |
| Linux Redhat 6.x                     |
| Linux Redhat 7.x                     |
| Linux Debian 8                       |
| FreeBSD 10.x                         |
| Microsoft Windows 2012 R2            |
| Microsoft Windows 2008               |

### Uyumlu Veritabanları

| Veritabanı           |
|--------------------|
| Sqlite 3.x         |
| Mysql >= 5.5       |
| MariaDB >= 10      |
| Postgresql >= 9.3  |

Hangi veritabanı seçmeli?

| Tip             | Kullanımı                                                  |
|-----------------|------------------------------------------------------------|
| Sqlite          | Tek kullanıcı veya küçük ekip (neredeyse eşzamanlılık yok) |
| Mysql/Postgres  | Daha büyük ekip, yüksek erişilebilirlik yapılandırması     |

Sqlite'i NFS bağlarken kullanmayın; yalnızca hızlı G/Ç - I/O ile bir diskiniz olduğunda.

### Uyumlu Web Sunucuları

| WWeb Sunucu        |
|--------------------|
| Apache HTTP Server |
| Nginx              |
| Microsoft IIS      |

Kanboard, Apache ile çalışacak şekilde önceden yapılandırılmıştır (URL yeniden yazımı).

### PHP Versionları

| PHP Version    |
|----------------|
| PHP >= 5.3.9   |
| PHP 5.4        |
| PHP 5.5        |
| PHP 5.6        |
| PHP 7.x        |

### Gerekli PHP Eklentileri

| PHP Eklentisi              | Not                                  |
|----------------------------|--------------------------------------|
| pdo_sqlite                 | Sadece Sqlite kullanıyorsanız        |
| pdo_mysql                  | Sadece Mysql/MariaDB kullanıyorsanız |
| pdo_pgsql                  | Sadece Postgres kullanıyorsanız      |
| gd                         |                                      |
| mbstring                   |                                      |
| openssl                    |                                      |
| json                       |                                      |
| hash                       |                                      |
| ctype                      |                                      |
| session                    |                                      |
| ldap                       | Yalnızca LDAP kimlik doğrulaması için|
| Zend OPcache               | Tavsiye edilen                       |

### İsteğe bağlı PHP Eklentileri

| PHP Eklentisi              | Not                                              |
|----------------------------|--------------------------------------------------|
| zip                        | Kanboard'dan eklentileri kurmak için kullanılır  |

### Tavsiyeler

- Modern Linux veya Unix işletim sistemi.
- En iyi performanslar OPcode önbellekleme etkinleştirilmiş haliyle PHP'nin en son sürümü ile elde edilmiştir.

İstemci-Client tarafı
-----------

### Tarayıcı-Browsers

Mümkünse daima en yeni sürümü olan modern bir tarayıcı kullanın:

| Tarayıcı                              |
|---------------------------------------|
| Safari                                |
| Google Chrome                         |
| Mozilla Firefox                       |
| Microsoft Internet Explorer >= 11     |
| Microsoft Edge                        |

### Cihazlar-PC ler

| Cihaz                  | Ekran çözünürlüğü  |
|------------------------|--------------------|
| Dizüstü veya masaüstü  | >= 1366 x 768      |
| Tablet                 | >= 1024 x 768      |
