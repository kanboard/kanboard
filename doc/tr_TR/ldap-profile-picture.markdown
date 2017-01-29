LDAP Kullanıcı Profili Fotoğrafı
=======================

Kanboard otomatik olarak kullanıcı resimlerini LDAP sunucusundan indirebilir.

Bu özellik, yalnızca LDAP kimlik doğrulaması etkinleştirildiğinde ve `LDAP_USER_ATTRIBUTE_PHOTO` parametresi tanımlanırsa etkinleştirilir.

Yapılandırma
-------------

`config.php` dosyasında, görüntüyü saklamak için kullanılan LDAP özniteliğini ayarlamanız gerekir.

```php
define('LDAP_USER_ATTRIBUTE_PHOTO', 'jpegPhoto');
```

Genellikle, `jpegPhoto` veya `thumbnailPhoto` nitelikleri kullanılır.
Görüntü JPEG veya PNG formatında saklanabilir.

Görüntüyü kullanıcı profiline yüklemek için, Active Directory yöneticileri [AD Photo Edit](http://www.cjwdev.co.uk/Software/ADPhotoEdit/Info.html) gibi yazılımları kullanır.

Notes
-----

Profil resmi **kullanıcı daha önce bir resim yüklememişse giriş yaparken indirilir**.

Kullanıcı fotoğrafını değiştirmek için önceki fotoğrafı kullanıcı profilinden el ile kaldırmanız gerekir.
