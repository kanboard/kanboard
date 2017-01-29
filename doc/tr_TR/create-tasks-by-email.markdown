E-posta ile görevler oluşturma
=====================

Bir e-posta göndererek görevleri doğrudan oluşturabilirsiniz.
Bu özellik eklentiler kullanarak ulaşılabilir.

Şu anda Kanboard, 3 harici hizmetler ile entegre edilmiştir:

- [Mailgun](https://github.com/kanboard/plugin-mailgun)
- [Sendgrid](https://github.com/kanboard/plugin-sendgrid)
- [Postmark](https://github.com/kanboard/plugin-postmark)

Bu hizmetler, herhangi bir SMTP sunucusunu yapılandırmak zorunda kalmadan gelen e-postaları işlemektedir.

Bir e-posta alındığında, Kanboard belirli bir son noktadaki mesajı alır.
Tüm karmaşık çalışmalar bu hizmetler tarafından zaten gerçekleştirilmektedir.

Gelen e-postaların iş akışı
------------------------

1. Belli bir adrese bir e-posta gönderiyorsunuz, örneğin **something+myproject@inbound.mydomain.tld**
2. E-postanız, üçüncü parti SMTP sunucularına yönlendirilir
3. SMTP sağlayıcısı, Kanboard web kancasını-hook  E-postayla JSON veya çok parçalı / form-veri formatlarını çağırır
4. Kanboard, alınan e-postayı ayrıştırır ve doğru projeye göre görev oluşturur.

Not: Yeni görevler otomatik olarak ilk kolonda oluşturulur.

E-posta biçimi
------------

- E-posta adresinin yerel kısmı artı ayırıcıyı kullanmalıdır, örneğin ** kanboard + project123 **
- Artı işaretinden sonra tanımlanan dize, bir proje tanımlayıcısıyla eşleşmelidir, örneğin **project123** ** projenin tanımlayıcısı **Proje 123**
- E-posta konusu görev başlığı haline gelir
- E-posta gövdesi görev açıklaması olur (Markdown biçimi)

Gelen e-postalar metin veya HTML biçiminde yazılabilir.
**Kanboard, basit HTML e-postalarını Markdown** a dönüştürebilir.

Güvenlik ve gereksinimler
-------------------------

- Kanboard web kancası rastgele bir tokenla korunuyor
- Gönderenin e-posta adresi bir Kanboard kullanıcısı ile eşleşmelidir
- Kanboard projesinin benzersiz bir tanımlayıcısı olmalıdır, örneğin **PROJEM**
- Kanboard kullanıcısının projeye üye olması gerekir
