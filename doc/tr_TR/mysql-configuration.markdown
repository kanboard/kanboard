Mysql / MariaDB Yapılandırması
===========================

Varsayılan olarak Kanboard, verilerini depolamak için Sqlite'i kullanır.
Ancak Mql veya MariaDB yerine Sqlite kullanmak mümkündür.

Gereksinimler
------------

- MySQL sunucusu
- PHP'nin `pdo_mysql` uzantısı kuruldu

Not: Kanboard, **Mysql> = 5.5 ve MariaDB> = 10.0** ile test edilmiştir

MySQL yapılandırması
-------------------

### Veritabanı oluşturma

İlk adım, MySQL sunucunuzda bir veritabanı oluşturmaktır.
Örneğin, mysql istemcisi komut satırıyla bunu yapabilirsiniz:

```sql
CREATE DATABASE kanboard;
```

### Bir yapılandırma dosyası oluştur

`config.php` dosyasında bu değerler bulunmalıdır:

```php
<?php

// We choose to use Mysql instead of Sqlite
define('DB_DRIVER', 'mysql');

// Mysql parameters
define('DB_USERNAME', 'REPLACE_ME');
define('DB_PASSWORD', 'REPLACE_ME');
define('DB_HOSTNAME', 'REPLACE_ME');
define('DB_NAME', 'kanboard');
```

Not: Ayrıca, `config.default.php` şablon dosyasını `config.php` olarak yeniden adlandırabilirsiniz.

### SQL dökümünü alma (alternatif yöntem)

İlk kez, Kanboard her bir veritabanı geçiş işleminde tek tek çalışacak ve bu işlem konfigürasyonunuza göre biraz zaman alabilir.

Olası bir zaman aşımını önlemek için, veritabanını doğrudan SQL şemasını içe aktararak başlatabilirsiniz:

```bash
mysql -u root -p my_database < app/Schema/Sql/mysql.sql
```

`app/Schema/Sql/mysql.sql` dosyası, veritabanının son sürümünü gösteren bir SQL dökümüdür.

SSL yapılandırması
-----------------

Bu parametrelerin Mysql SSL bağlantısını etkinleştirmek için tanımlanması gerekir:

```php
// Mysql SSL key
define('DB_SSL_KEY', '/path/to/client-key.pem');

// Mysql SSL certificate
define('DB_SSL_CERT', '/path/to/client-cert.pem');

// Mysql SSL CA
define('DB_SSL_CA', '/path/to/ca-cert.pem');
```
