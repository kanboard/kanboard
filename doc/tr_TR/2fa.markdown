Çift-Kademeli Kimlik Doğrulama
=========================

Her kullanıcı [Çift-Kademeli Kimlik Doğrulama two-factor authentication](http://en.wikipedia.org/wiki/Two_factor_authentication) yı aktyif edebilir.
Başarılı bir oturum açtıktan sonra, kullanıcıya Kanboard'a erişim izni vermeleri için bir kerelik kod (6 karakter) istenecektir.

Bu kod, genellikle akıllı telefonunuza takılı olan uyumlu bir yazılım tarafından sağlanmalıdır.

Kanboard, [RFC 6238] (http://tools.ietf.org/html/rfc6238) içinde tanımlanan [Zamana Dayalı Bir Zamanlık Şifre Algoritması Time-based One-time Password Algorithm] (http://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm) kullanır.

Standart TOTP sistemi ile uyumlu birçok yazılım bulunmaktadır.
Örneğin, şu uygulamaları kullanabilirsiniz:

- [Google Authenticator](https://github.com/google/google-authenticator/) (Android, iOS, Blackberry)
- [FreeOTP](https://fedorahosted.org/freeotp/) (Android, iOS)
- [OATH Toolkit Araç Seti](http://www.nongnu.org/oath-toolkit/) (Unix/Linux'da Komut satırı yardımcı programı)

Bu sistem çevrimdışı çalışabilir ve mutlaka cep telefonunuz olması gerekmez.

Kurmak
-----

1. Kullanıcı profilinize git
2. Sol tarafta **İki faktörlü kimlik doğrulama** seçeneğini tıklayın ve kutuyu işaretleyin
3. Sizin için gizli bir anahtar oluşturulur.

![2FA](screenshots/2fa.png)

- TOTP yazılımında gizli anahtarı kaydetmeniz gerekir. Akıllı telefon kullanıyorsanız, en kolay çözüm QR kodunu FreeOTP veya Google Authenticator ile taramaktır.
- Her sefer yeni bir oturum açtığınızda, yeni bir kod sorulur
- Oturumunuzu kapatmadan önce cihazınızı test etmeyi unutmayın

Bu özelliği her etkinleştirirken / devre dışı bıraktığınızda yeni bir gizli anahtar oluşturulur.
