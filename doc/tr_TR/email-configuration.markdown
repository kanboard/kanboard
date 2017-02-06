E-posta yapılandırması
===================

Kullanıcı ayarları
-------------

E-posta bildirimlerini almak için, Kanboard kullanıcıları şunları yapmalıdır:

- Profillerinde etkinleştirilmiş bildirimler
- Profillerinde geçerli bir e-posta adresiniz olsun
- Bildirimleri tetikleyecek projenin bir üyesi olun

Not: İşlemi gerçekleştiren oturum açmış kullanıcı herhangi bir bildirim almıyor, yalnızca diğer proje üyeleri.

E-posta taşımaları
----------------

Kullanabileceğiniz birkaç e-posta ulaşımı vardır:

- SMTP
- Sendmail
- PHP doğal-native posta fonksiyonu
- Diğer yöntemler harici eklentiler tarafından sağlanabilir: Postmark, Sendgrid ve Mailgun

Sunucu ayarları
---------------

Varsayılan olarak, Kanboard, e-postalar göndermek için birlikte verilen PHP posta işlevini kullanacaktır.
Sunucunuz zaten e-posta gönderebiliyorsa genellikle yapılandırma gerektirmez.

Bununla birlikte, SMTP protokolü ve Sendmail gibi diğer yöntemleri kullanmak mümkündür.

### SMTP yapılandırması

`config.default.php` dosyasını `config.php` olarak yeniden adlandırın ve bu değerleri değiştirin:

```php
// We choose "smtp" as mail transport
define('MAIL_TRANSPORT', 'smtp');

// We define our server settings
define('MAIL_SMTP_HOSTNAME', 'mail.example.com');
define('MAIL_SMTP_PORT', 25);

// Credentials for authentication on the SMTP server (not mandatory)
define('MAIL_SMTP_USERNAME', 'username');
define('MAIL_SMTP_PASSWORD', 'super password');
```

Güvenli bir bağlantı, TLS veya SSL kullanmak da mümkündür:

```php
define('MAIL_SMTP_ENCRYPTION', 'ssl'); // Valid values are "null", "ssl" or "tls"
```

### Sendmail yapılandırması

Varsayılan olarak sendmail komutu `/usr/sbin/sendmail -bs` olacaktır, ancak bunu yapılandırma dosyanızda özelleştirebilirsiniz.

Örnek:

```php
// We choose "sendmail" as mail transport
define('MAIL_TRANSPORT', 'sendmail');

// If you need to change the sendmail command, replace the value
define('MAIL_SENDMAIL_COMMAND', '/usr/sbin/sendmail -bs');
```

### PHP doğal-native posta fonksiyonu

Varsayılan yapılandırma şu şekildedir:

```php
define('MAIL_TRANSPORT', 'mail');
```

### Gönderenin e-posta adresi

Varsayılan olarak, e-postalar gönderenin adresini `notifications@kanboard.local` kullanacaktır.
Bu adrese cevap vermek mümkün değildir.

Bu adresi, yapılandırma dosyanızdaki `MAIL_FROM` sabitinin değerini değiştirerek özelleştirebilirsiniz.

```php
define('MAIL_FROM', 'kanboard@mydomain.tld');
```

SMTP sunucusu yapılandırmanız varsayılan adresi kabul etmiyorsa bu yararlı olabilir.

### Bildirimlerdeki göreve bir bağlantı nasıl görüntülenir?

Bunu yapmak için, [Uygulama Ayarları](https://kanboard.net/documentation/application-configuration) bölümünde Kanboard kurulumunuzun URL'sini belirtmeniz gerekir. 
Varsayılan olarak, hiçbir şey tanımlanmadığından hiçbir bağlantı görüntülenmez.

Örnekler:

- http://demo.kanboard.net/
- http://myserver/kanboard/
- http://kanboard.mydomain.com/

Bitiş eğik çizgisini`/` unutmayın.

Kanboard, URL'yi bir komut satırı komut dosyasından tahmin edemediğinden ve bazı kişilerin çok spesifik bir yapılandırmaya sahip olmasından bunu manuel olarak tanımlamanız gerekir.

Sorun giderme
---------------

E-posta gönderilmiyorsa ve her şeyin doğru yapılandırıldığından eminseniz:

- Spam dosyanı kontrol et
- Hata ayıklama modunu etkinleştirin ve `data/debug.log` hata ayıklama dosyasını kontrol edin, tam hatayı görmelisiniz
- Sunucunuzun veya barındırma sağlayıcınızın e-postalar göndermenize izin verdiğinden emin olun
- SeLinux'u kullanırsanız, PHP'nin e-postaları göndermesine izin verin


