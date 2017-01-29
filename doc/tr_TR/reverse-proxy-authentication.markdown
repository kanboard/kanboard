Ters-Reverse Proxy Kimlik Doğrulaması
============================

Bu kimlik doğrulama yöntemi genellikle büyük kuruluşlar için [SSO](http://en.wikipedia.org/wiki/Single_sign-on) (Tek Oturum Açma/Single Sign-On) için kullanılır.

Kimlik doğrulama başka bir sistem tarafından yapılır, Kanboard şifrenizi bilmiyor ve siz zaten kimliği doğrulanmış olduğunu varsayalım.

Gereksinimler
------------

- İyi yapılandırılmış bir ters proxy

veya

- Aynı sunucuda Apache Kimliği


Bu nasıl çalışıyor?
-------------------

1. Geri vekiliniz, kullanıcının kimliğini doğrular ve kullanıcı adını bir HTTP üstbilgisi aracılığıyla gönderir.
2. Kanboard talepten kullanıcı adını alır
	- Kullanıcı gerekirse otomatik olarak oluşturulur
	- Geçerli olduğunu varsayan herhangi bir istemeden yeni bir Kanboard oturumu açın

Kurulum Talimatları
-------------------------

### Ters proxy ayarlarınızı yapma

Bu, bu dokümantasyon kapsamında değildir.
Kullanıcı girişinin bir HTTP üstbilgisi kullanarak ters proxy tarafından gönderildiğini kontrol etmeli ve bunlardan hangisinin olduğunu öğrenmelisin.

### Kanboard'u Kurma

Özel bir `config.php` dosyası oluşturun veya  `config.default.php` dosyasını kopyalayın:


```php
<?php

// Enable/disable reverse proxy authentication
define('REVERSE_PROXY_AUTH', true); // Set this value to true

// The HTTP header to retrieve. If not specified, REMOTE_USER is the default
define('REVERSE_PROXY_USER_HEADER', 'REMOTE_USER');

// The default Kanboard admin for your organization.
// Since everything should be filtered by the reverse proxy,
// you should want to have a bootstrap admin user.
define('REVERSE_PROXY_DEFAULT_ADMIN', 'myadmin');

// The default domain to assume for the email address.
// In case the username is not an email address, it
// will be updated automatically as USER@mydomain.com
define('REVERSE_PROXY_DEFAULT_DOMAIN', 'mydomain.com');
```

Notlar:

- Proxy, Kanboard'u çalıştıran aynı web sunucusuysa, [CGI protokolü](http://www.ietf.org/rfc/rfc3875) uyarınca, başlık adı `REMOTE_USER` olacaktır. Örnek olarak, Apache, `Require valid-user` parametresi ayarlanmışsa varsayılan olarak `REMOTE_USER` ekler.

- Apache, Kanboard çalıştıran başka bir Apache için bir ters proxy ise, `REMOTE_USER` başlığı ayarlanmamıştır (IIS ve Nginx ile aynı davranış).

- Gerçek bir ters proxyğiniz varsa, [HTTP ICAP taslağı](http://tools.ietf.org/html/draft-stecher-icap-subid-00#section-3.4) üstbilgisini  `X-Authenticated-User` Kimliği Doğrulanmış Kullanıcı. Bu fiili standart birçok araç tarafından benimsenmiştir.

