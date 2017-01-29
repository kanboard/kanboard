Windows 2008/2012 üzerinde IIS ile Yükleme
==========================================

Bu kılavuz, Kanboard'un IIS ve PHP ile Windows Server'da adım adım kurulmasına yardımcı olacaktır.

PHP kurulumu
----------------

- IIS'yi sunucunuza yükleyin (yeni bir rol ekleyin ve CGI/FastCGI'yi etkinleştirmeyi unutmayın)
- Resmi belgeleri izleyerek PHP'yi kurun:
	- [Microsoft IIS 5.1 ve IIS 6.0](http://php.net/manual/tr/install.windows.iis6.php)
	- [Microsoft IIS 7.0 ve sonrası](http://php.net/manual/en/install.windows.iis7.php)
	- [PHP for Windows burada mevcuttur](http://windows.php.net/download/)


### PHP.ini

En azından bu uzantıları `php.ini` dosyanızda bulmanız gerekir:

```ini
extension=php_gd2.dll
extension=php_ldap.dll
extension=php_mbstring.dll
extension=php_openssl.dll
extension=php_pdo_sqlite.dll
```

Gerekli PHP uzantılarının tam listesi [gereksinimler sayfasında](requirements.markdown) dır.

Saat dilimini ayarlamayı unutmayın:

```ini
date.timezone = Europe/Istanbul
```

Desteklenen saat dilimleri listesi [PHP belgelerinde](http://php.net/manual/tr/timezones.europe.php) bulunabilir.

Notlar:

- PHP <5.4 kullanıyorsanız, php.ini dosyanızdaki kısa etiketleri etkinleştirmeniz gerekir
- Yukarıda belirtilen gerekli php uzantılarını etkinleştirmeyi unutmayın
- "MSVCP110.dll kütüphanesi eksik / the library MSVCP110.dll is missing" konusunda bir hata alırsanız, Microsoft web sitesinden Visual Studio için Visual C ++ Yeniden Dağıtılabilir (Visual C++ Redistributable for Visual Studio) dosyasını indirmeniz gerekebilir.

IIS Modülleri
-----------

Kanboard arşivi(sıkıştırılmış dosya) [URL yeniden yazma](nice-urls.markdown) özelliğini etkinleştirmek için bir `web.config` dosyası içerir.
Bu yapılandırma [IIS için yeniden yazma modülü-Rewrite module for IIS](http://www.iis.net/learn/extensions/url-rewrite-module/using-the-url-rewrite-module) gerektirir.

Yeniden yazma modülünüz yoksa, IIS'den bir dahili sunucu hatası (500) alırsınız.
Kanboard'da okunabilir-hoş URL'lerle sahip olmak istemiyorsanız `web.config` dosyasını kaldırabilirsiniz.

Kanboard kurulumu
---------------------

- Zip-arşivi(sıkıştırılmış dosya) dosyasını indirin
- Arşivi(sıkıştırılmış dosya) `C:\inetpub\wwwroot\kanboard` klasörüne sıkıştırılmış dosyaları açınız
- `veri 'dizininin IIS kullanıcısı tarafından yazılabilir olduğundan emin olun
- Kanboard'u kullanmak için web tarayıcınızı açın http: // localhost / kanboard /
- Varsayılan kimlik bilgileri **admin/admin**
- [URL yeniden yazma yapılandırması](nice-urls.markdown)

Notlar
-----

- Kanboard'un bazı özellikleri, [günlük arka plan işi-cronjob](cronjob.markdown) çalıştırmanızı gerektirir.

