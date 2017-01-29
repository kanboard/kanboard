FreeBSD 10 Kurulum
=======================

Paketlerden yükle
---------------------

```bash
$ pkg update
$ pkg upgrade
$ pkg install apache24 mod_php56 kanboard
```

`/etc/rc.conf` dosyanızdaki Apache'yi etkinleştirin:

```bash
$ echo apache24_enable="YES" >> /etc/rc.conf
```

Apache için PHP yi kurun:

```bash
$ echo "AddType application/x-httpd-php .php" >> /usr/local/etc/apache24/Includes/php.conf
$ echo "DirectoryIndex index.php index.html" >> /usr/local/etc/apache24/Includes/php.conf
```

Sonra Apache'yi başlatın:

```bash
$ service apache24 start
```

Kanboard klasörüne, Apache docroot'unuza sembolik bağ ekleyin:

```bash
cd /usr/local/www/apache24/data
ln -s /usr/local/www/kanboard
```

http://your.server.domain.tld/ adresine gidin ve tadını çıkarın!

*Notlar*:
- LDAP entegrasyonu gibi ek özellikler kullanmak istiyorsanız; Lütfen pkg'yi kullanarak uygun PHP modülünü kurun.
- Klasör verisinin izinlerini ayarlamanız gerekebilir

Bağlantı noktalarından yükleme
---------------------

Genellikle 3 eleman kurulmalıdır:

- Apache
- Apache için mod_php
- Kanboard

Bağlantı noktaları getir ve çıkar

```bash
$ portsnap fetch
$ portsnap extract
```

Veya mevcut haliyle güncelleme:

```bash
$ portsnap fetch
$ portsnap update
```

Portsnap ile ilgili daha fazla bilgi [FreeBSD Handbook](https://www.freebsd.org/doc/handbook/ports-using.html)  'da  bulunabilir.

Apache'yi kur:

```bash
$ cd /usr/ports/www/apache24
$ make install clean
```
Enable Apache in your `/etc/rc.conf`:

```bash
$ echo apache24_enable="YES" >> /etc/rc.conf
```

Apache için mod_php'yi kurun:

```bash
$ cd /usr/ports/www/mod_php5
$ make install clean
```

Kanboard form bağlantı noktalarını yükle:

```bash
$ cd /usr/ports/www/kanboard
$ make install clean
```

Kanboard formu portları yükleyin

```bash
$ echo "AddType application/x-httpd-php .php" >> /usr/local/etc/apache24/Includes/php.conf
$ echo "DirectoryIndex index.php index.html" >> /usr/local/etc/apache24/Includes/php.conf
```

Sonra Apache'yi başlatın:

```bash
$ service apache24 start
```

http://your.server.domain.tld/kanboard  adresine gidin ve tadını çıkarın!

*Not*:
LDAP entegrasyonu gibi ek özellikleri kullanmak isterseniz
Lütfen `lang/php5-extensions` den uygun PHP eklentisi-modülünü kurun.

El ile kurulum
-------------------

Sürüm 1.0.16 itibariyle Kanboard FreeBSD portlarında bulunabilir
Elle kurmanıza gerek yoktur.

Lütfen aklınızda bulundurun
-----------

- Portlar [bitbucket](https://bitbucket.org/if0/freebsd-kanboard/) üzerinde barındırılıyor . Yorum yapmaktan çekinmeyin
fork edin ve güncelleme önermek için çekinmeyin!
- Kanboard'un bazı özellikleri, [günlük arka plan işi-cronjob](cronjob.markdown) çalıştırmanızı gerektirir.
