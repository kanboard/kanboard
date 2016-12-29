Postgresql yapılandırması
========================

Varsayılan olarak Kanboard, verilerini depolamak için Sqlite'i kullanır, ancak Postgresql'i kullanmak da mümkündür.

Gereksinimler
------------

- Postgresql >= 9.3
- `pdo_pgsql` PHP uzantısı kurulmuş olmalı (Debian/Ubuntu: `apt-get install php5-pgsql`)

Configuration
-------------

### `pgsql` komutu ile boş bir veritabanı oluşturun:

```sql
CREATE DATABASE kanboard;
```

### Bir yapılandırma dosyası oluşturun

`config.php` dosyasında aşağıdaki şu değerler bulunmalıdır:

```php
<?php

// PostgreSQL'i Sqlite yerine kullanmayı seçiyoruz
// We choose to use Postgresql instead of Sqlite
define('DB_DRIVER', 'postgres');

// Mysql parameters
define('DB_USERNAME', 'REPLACE_ME');
define('DB_PASSWORD', 'REPLACE_ME');
define('DB_HOSTNAME', 'REPLACE_ME');
define('DB_NAME', 'kanboard');
```

Not: Ayrıca, `config.default.php` şablon dosyasını `config.php` olarak yeniden adlandırabilirsiniz.

### SQL dökümünü-dump alma (alternatif yöntem)

Başlangıçta bri kez, Kanboard her bir veritabanı geçiş işleminde tek tek çalışacak ve bu işlem konfigürasyonunuza göre biraz zaman alabilir.

Sorunları veya olası zaman aşımlarını önlemek için, veritabanını doğrudan SQL şemasını içe aktararak başlatabilirsiniz:

```bash
psql -U postgres my_database < app/Schema/Sql/postgres.sql
```

`app/Schema/Sql/postgres.sql` dosyası, veritabanının son sürümünü gösteren bir SQL dökümüdür.
