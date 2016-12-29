Arka Plan Çalışanları-Workers
==================

**Bu özellik deneyseldir**.

Yapılandırmanıza bağlı olarak, bazı özellikler HTTP isteği ile aynı işlemde yürütülürse uygulamayı yavaşlatabilir.
Kanboard, bu görevleri gelen olayları dinleyen bir arka plan işçisine devredebilir.

Kanboard'u yavaşlatabilecek özellik örneği:

- Harici bir SMTP sunucusu üzerinden e-posta göndermek birkaç saniye sürebilir
- Dış hizmetleri bildirim gönderme

Bu özellik isteğe bağlıdır ve sunucunuza bir sıra arka plan programının yüklenmesini gerektirir.

### Beanstalk

[Beanstalk](http://kr.github.io/beanstalkd/) basit, hızlı bir iş kuyruğu.

- Beanstalk'u kurmak için, Linux dağıtımınızın paket yöneticisini kullanabilirsiniz
- [Beanstalk için Kanboard eklentisi](https://kanboard.net/plugin/beanstalk)
- Çalışanı Kanboard komut satırı aracıyla çalıştırın: `./cli worker`

### RabbitMQ

[RabbitMQ](https://www.rabbitmq.com/), yüksek kullanılabilirlikli altyapı için daha uygun olan sağlam bir mesajlaşma sistemidir.

- Kurulum ve yapılandırma için RabbitMQ'nun resmi belgelerini takip edin
- [RabboardMQ için Kanboard eklentisi](https://kanboard.net/plugin/rabbitmq)
- Çalışanı Kanboard komut satırı aracıyla çalıştırın: `./cli worker`

### Notlar

- Kanboard çalışana bir süreç denetçisi (systemd, upstart veya supervisord) ile başlanmalıdır.
- Dosyaları yerel dosya sisteminde saklıyorsanız veya Sqlite'i kullanıyorsanız işlemin veri klasörüne erişmesi gerekir


