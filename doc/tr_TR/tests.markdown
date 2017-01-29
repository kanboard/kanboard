Otomatik testler
===============

[PHPUnit](https://phpunit.de/), Kanboard'da otomatik testler yapmak için kullanılır.

Sonuçların her yerde aynı olduğundan emin olmak için farklı veritabanlarında (Sqlite, Mysql ve PostgreSQL) testler çalıştırabilirsiniz.

Gereksinimler
------------

- Linux/Unix machine
- PHP
- PHPUnit yüklü
- Mysql ve Postgresql (isteğe bağlı)
- Selenium (isteğe bağlı)
- Firefox (isteğe bağlı)

Birim Testleri
----------

### Sqlite ile sınama

Sqlite testlerinde bellek içi bir veritabanı kullanılır, dosya sisteminde hiçbir şey yazılmaz.

PHPUnit yapılandırma dosyası `tests/units.sqlite.xml`.
Kanboard dizininizden `phpunit -c tests/units.sqlite.xml` komutunu çalıştırın.

Örnek:

```bash
phpunit -c tests/units.sqlite.xml

PHPUnit 5.0.0 by Sebastian Bergmann and contributors.

...............................................................  63 / 649 (  9%)
............................................................... 126 / 649 ( 19%)
............................................................... 189 / 649 ( 29%)
............................................................... 252 / 649 ( 38%)
............................................................... 315 / 649 ( 48%)
............................................................... 378 / 649 ( 58%)
............................................................... 441 / 649 ( 67%)
............................................................... 504 / 649 ( 77%)
............................................................... 567 / 649 ( 87%)
............................................................... 630 / 649 ( 97%)
...................                                             649 / 649 (100%)

Time: 1.22 minutes, Memory: 151.25Mb

OK (649 tests, 43595 assertions)
```

### Mysql ile Test

Localhost'da MySQL veya MariaDb yüklü olmalıdır.

Varsayılan olarak, bu kimlik bilgileri kullanılır:

- Ana makine adı: **localhost**
- Kullanıcı adı: **kök**
- Şifre: yok
- Veritabanı: **kanboard_unit_test**

Her yürütme için veritabanı silinir ve yeniden oluşturulur.

PHPUnit yapılandırma dosyası  `tests/units.postgres.xml` dir.
Kanboard dizininizden `phpunit -c  `phpunit -c tests/units.postgres.xml`  komutunu çalıştırın.

### Postgresql ile test edin

Postgresql'in localhost'a kurulmuş olması gerekir.

Varsayılan olarak, bu kimlik bilgileri kullanılır:

- Ana makine adı: **localhost**
- Kullanıcı adı: **postgres**
- Şifre: yok
- Veritabanı: **kanboard_unit_test**

'Postgres` kullanıcısının veritabanlarını oluşturup açmasına izin verdiğinizden emin olun.
Veritabanı her yürütme için yeniden oluşturulur.

PHPUnit yapılandırma dosyası `tests/units.postgres.xml` dir.
Kanboard dizininizden `phpunit -c tests/units.postgres.xml` komutunu çalıştırın.

Entegrasyon Testleri
-----------------

Entegrasyon testleri esas olarak API'yi test etmek için kullanılır.
Test paketleri, bir kapsayıcı içinde çalışan uygulamaya gerçek HTTP çağrıları yapıyor.

### Gereksinimler

- PHP
- Composer
- Unix işletim sistemi (Mac OS veya Linux)
- Docker
- Docker Compose

### Uyum testlerini çalıştırmak

Entegrasyon testleri Docker konteynerlerini kullanıyor.
Desteklenen her bir veritabanına karşı testleri çalıştırmak için kullanılabilecek 3 farklı ortam vardır.

Her bir test paketini çalıştırmak için aşağıdaki komutları kullanabilirsiniz:

```bash
# Run tests with Sqlite
make integration-test-sqlite

# Run tests with Mysql
make integration-test-mysql

# Run tests with Postgres
make integration-test-postgres
```

Kabul Deneyleri
----------------

Kabul testleri (bazen uçtan uca testler ve işlevsel testler olarak da bilinir), Selenium'u kullanan bir tarayıcıda UI'nın gerçek işlevselliğini test eder.

Bu testleri çalıştırmak için [Selenium Standalone Server] (http://www.seleniumhq.org/download/) ve Firefox'un uyumlu bir sürümü yüklü olmalıdır.

PHPUnit yapılandırma dosyası `tests / acceptance.xml` dir.
Selenyum ve Kanboard uygulaması çalışıyorken, Kanboard dizininizden `make test-browser` komutunu çalıştırın. Bu, test paketini başlatacak ve Firefox'un otomatik olarak açıldığını göreceksiniz ve kabul testlerinde belirtilen işlemleri uygulayacaksınız.

Örnek:

```bash
$ make test-browser
PHPUnit 4.8.26 by Sebastian Bergmann and contributors.

..

Time: 5.59 seconds, Memory: 5.25MB

OK (2 tests, 5 assertions)
```


Travis-CI ile Sürekli Entegrasyon
-------------------------------------

Her taahhüt ana depoda itildikten sonra, birim testleri PHP'nin 5 farklı versiyonu boyunca yürütülür:

- PHP 7.0
- PHP 5.6
- PHP 5.5
- PHP 5.4
- PHP 5.3

Each version of PHP is tested against the 3 supported database: Sqlite, Mysql and Postgresql.

The Travis config file  `.travis.yml` is located on the root directory of Kanboard.

PHP'nin her sürümü desteklenen 3 veritabanına karşı test edilmiştir: Sqlite, Mysql ve Postgresql.

Travis yapılandırma dosyası `.travis.yml` , Kanboard'un kök dizininde bulunur.
