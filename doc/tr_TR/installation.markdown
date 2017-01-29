Kurulum Talimatları
============

Öncelikle, daha ileri gitmek için önce [gereksinimler](requirements.markdown) kontrol edin.

Arşivden (kararlı sürüm)
---------------------------------

1. PHP'nin yüklü olduğu bir web sunucunuz olmalıdır
2. Kaynak kodu indirin ve istediğiniz yere `kanboard` dizini kopyalayın
3. `data` dizininin web sunucusu kullanıcısı tarafından yazılabilir olup olmadığını kontrol edin
4. Tarayıcınızla <http://yourpersonalserver/kanboard> adresine gidin
5. Varsayılan kullanıcı adı ve şifre **admin/admin**
6. Kanboard yazılımını kullanmaya başlayın
7. Parolanızı değiştirmeyi unutmayın!

`data` klasörü depolamak için kullanılır:

Sqlite veritabanı: `db.sqlite`
- Hata ayıklama dosyası: `debug.log` (hata ayıklama modu etkinleştirilmişse)
- Yüklenen dosyalar: `files/*`
- Resim tırnak-thumb ları: `files/thumbnails/*`

Uzak bir veritabanı (Mysql/Postgresql) ve uzak nesne depolama birimi (Aws S3 veya benzeri) kullananların mutlaka kalıcı bir yerel veri klasörüne sahip olmaları veya izinlerini değiştirmeleri gerekmez.

Git deposundan (geliştirme versiyonu)
---------------------------------------------

Bu yöntemi kullanmak için [composer](https://getcomposer.org/) yüklemelisiniz.

1. `git clone https://github.com/kanboard/kanboard.git`
2. `composer install --no-dev`
3. Yukarıdaki üçüncü adıma geçin

Not: Bu yöntem, **mevcut geliştirme sürümünü** yükleyecektir, kendi sorumluluğunuzdadır.

Belge kökü-root dışında kurulum
-----------------------------------------

Kanboard'u web sunucusu belge kökü dışında yüklemek isterseniz, en azından bu sembolik bağları-symlinks oluşturmanız gerekir:

```bash
.
├── assets -> ../kanboard/assets
├── cli -> ../kanboard/cli
├── doc -> ../kanboard/doc
├── favicon.ico -> ../kanboard/favicon.ico
├── index.php -> ../kanboard/index.php
├── jsonrpc.php -> ../kanboard/jsonrpc.php
└── robots.txt -> ../kanboard/robots.txt
```

İçeriği doğrudan Apache yapılandırmasına eklenebileceğinden `.htaccess` isteğe bağlıdır.

[Yapılandırma dosyası-Config file](config.markdown) değiştirerek eklentiler ve dosyalar klasörleri için özel bir yer de tanımlayabilirsiniz.


Diğer Veritabanı Türleri
--------------------

Kanboard, MySQL ve Postgres'i Sqlite'e alternatif olarak desteklemektedir.

- [Mysql yapılandırması](mysql-configuration.markdown)
- [Postgres yapılandırması](postgresql-configuration.markdown)

İsteğe Bağlı Kurulum
---------------------

- Kanboard'un bazı özellikleri, [günlük arka plan işi-cronjob](cronjob.markdown) çalıştırmanızı gerektirir (Raporlar ve analizler için) .
- Performansı artırmak için [Arka plan çalışanını kurun](worker.markdown) 

Güvenlik
--------

- Varsayılan kullanıcı/şifreyi değiştirmeyi unutmayın
- Herkese URL'den `data` dizinine erişmesine izin verme. Apache için zaten bir `.htaccess` ve IIS için bir `web.config` dosyası var, ancak diğer web sunucuları için bir şey yok.
