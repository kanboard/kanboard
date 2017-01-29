Eklenti(Plugin) Geliştirme
==================

Not: Eklenti API'sı şu an **alfa olarak kabul edilmektedir**.

Eklentiler, Kanboard'un temel işlevlerini genişletmek, özellikler eklemek, temalar oluşturmak veya varsayılan davranışı değiştirmek için kullanışlıdır.

Eklenti yaratıcıları açıkça Kanboard'un uyumlu sürümlerini belirtmelidir. Kanboard'un dahili kodu zamanla değişebilir ve eklentiniz yeni sürümlerle test edilmelidir. Değişiklikler için lütfen [Değişiklik Günlüğü](https://github.com/kanboard/kanboard/blob/master/ChangeLog) kontrol edin.

- [Eklenti oluşturma](plugin-registration.markdown)
- [Eklenti kancalarını kullanma](plugin-hooks.markdown)
- [Etkinlikler](plugin-events.markdown)
- [Varsayılan uygulama davranışlarını geçersiz kıl](plugin-overrides.markdown)
- [Eklentiler için transfer şemaları ekle](plugin-schema-migrations.markdown)
- [Özel rutlar](plugin-routes.markdown)
- [Yardımcı ekle](plugin-helpers.markdown)
- [Posta aktarımları ekle](plugin-mail-transports.markdown)
- [Bildirim türlerini ekle](plugin-notifications.markdown)
- Otomatik işlemler ekle](plugin-automatic-actions.markdown)
- [Meta verileri kullanıcılara, görevlere ve projelere ekleme](plugin-metadata.markdown)
- [Kimlik doğrulama mimarisi](plugin-authentication-architecture.markdown)
- [Kimlik doğrulama eklenti kaydı](plugin-authentication.markdown)
- [Yetkilendirme mimarisi](plugin-authorization-architecture.markdown)
- [Özel grup sağlayıcıları](plugin-group-provider.markdown)
- [Dış-harici link sağlayıcıları](plugin-external-link.markdown)
- [Dış görevler](plugin-external-tasks.markdown)
- [Avatar sağlayıcıları ekle](plugin-avatar-provider.markdown)
- [LDAP istemcisi](plugin-ldap-client.markdown)

Eklentilere örnekler
-------------------

- [SMS İki-Kademeli Kimlik Doğrulaması](https://github.com/kanboard/plugin-sms-2fa)
- [LDAP desteği ile Ters-Proxy Kimlik Doğrulaması](https://github.com/kanboard/plugin-reverse-proxy-ldap)
- [Slack](https://github.com/kanboard/plugin-slack)
- [Hipchat](https://github.com/kanboard/plugin-hipchat)
- [Jabber](https://github.com/kanboard/plugin-jabber)
- [Sendgrid](https://github.com/kanboard/plugin-sendgrid)
- [Mailgun](https://github.com/kanboard/plugin-mailgun)
- [Postmark](https://github.com/kanboard/plugin-postmark)
- [Amazon S3](https://github.com/kanboard/plugin-s3)
- [Bütçe planlaması](https://github.com/kanboard/plugin-budget)
- [Kullanıcı zaman çizelgeleri](https://github.com/kanboard/plugin-timetable)
- [Alt Görev Tahmini](https://github.com/kanboard/plugin-subtask-forecast)
- [Otomatik İşlem örneği](https://github.com/kanboard/plugin-example-automatic-action)
- [Tema eklentisi örneği](https://github.com/kanboard/plugin-example-theme)
- [CSS eklentisi örneği](https://github.com/kanboard/plugin-example-css)
