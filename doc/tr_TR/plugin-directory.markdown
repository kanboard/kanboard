Eklenti Dizini Yapılandırması
==============================

Eklentileri kullanıcı arayüzünden kurmak, güncellemek ve kaldırmak için şu gereksinimlere sahip olmanız gerekir:

- Eklenti dizini, web sunucusu kullanıcısı tarafından yazılabilir olmalıdır
- Zip uzantısı sunucunuzda mevcut olmalıdır
- `PLUGIN_INSTALLER` yapılandırma parametresi `true` olarak ayarlanmalıdır

Bu özelliği devre dışı bırakmak için yapılandırma dosyanızdaki PLUGIN_INSTALLER` değerini `false` olarak değiştirin.
Dosya sistemi üzerindeki eklenti klasörünün izinlerini de değiştirebilirsiniz.

Yalnızca yöneticilere kullanıcı arayüzünden eklentiler yüklemesine izin verilir.

Varsayılan olarak, yalnızca Kanboard'un web sitesinde listelenen eklenti mevcuttur.

