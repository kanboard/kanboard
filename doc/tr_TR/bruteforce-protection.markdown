(Brute Force)Kaba kuvvet Koruma
======================

Kanboard'un kaba kuvvet koruması, kullanıcı hesabı düzeyinde çalışır:

- Aynı kullanıcı adı için üç kimlik doğrulama hatası yapıldıktan sonra, oturum açma formu, otomatik bot hazırlayıcıları önlemek için bir captcha resmi görüntüler.
- 6 kimlik doğrulama başarısızlığından sonra, kullanıcı hesabı 15 dakika boyunca kilitlenir.

Bu özellik yalnızca oturum açma formunu kullanan kimlik doğrulama yöntemleri için kullanılabilir.

Bununla birlikte **kullanıcı API'sı aracılığıyla üç kimlik doğrulama hatasından sonra**, giriş formunu kullanarak hesabın kilidinin açılması gerekir.

Botlar, çeşitli anonim vekilleri kullanabileceğinden, Kanboard herhangi bir IP adresini engellemez. Bununla birlikte, büyük taramaları önlemek için [fail2ban] (http://www.fail2ban.org) gibi harici araçları kullanabilirsiniz.

Yapılandırma
-------------

Varsayılan ayarlar şu yapılandırma değişkenleriyle değiştirilebilir:

```php
// 3 kimlik doğrulama hatasından sonra captcha'yı etkinleştir
define('BRUTEFORCE_CAPTCHA', 3);

// 6 kimlik doğrulama hatasından sonra hesabı kilitle
define('BRUTEFORCE_LOCKDOWN', 6);

// Hesap süresini dakika olarak kilitle
define('BRUTEFORCE_LOCKDOWN_DURATION', 15);
```

Kullanıcıların kilidini açma
---------------

15 dakika beklemek istemiyorsanız kullanıcı arayüzünden bir kullanıcının kilidini kaldırabilirsiniz.
Yönetici olarak, kullanıcı profiline gidin ve "Bu kullanıcının kilidini aç" ı tıklayın.
