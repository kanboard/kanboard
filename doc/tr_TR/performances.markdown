Kanboard Performans
=====================

Yapılandırmanıza göre, bazı özellikler Kanboard kullanımını yavaşlatabilir. 
Varsayılan olarak, tüm işlemler eşzamanlıdır ve HTTP isteği ile aynı iş parçacığında gerçekleştirilir. 
Bu bir PHP kısıtlamasıdır. 
Ancak, bunu iyileştirmek mümkündür.

Yüklediğiniz eklentilere bağlı olarak, harici servislerle iletişim kurmak yüzlerce milisaniyeden fazla hatta saniyeler sürebilir.
Ana iş parçacığının engellenmesini önlemek için, bu işlemleri bir [arka plan çalışma](worker.markdown) havuzuna devretmek mümkündür.
Bu kurulum, altyapınıza ek yazılım yüklemenizi gerektirir.

Darboğazı nasıl tespit edebilirim?
-----------------------------

- Hata ayıklama modunu (debug mode) etkinleştir
- Günlük dosyasını (log) izleyin
- Kanboard'da bir şeyler yapın (örneğin bir görevi sürükleyip bırakın)
- Tüm işlemler yürütme süresi ile günlüğe kaydedilir (HTTP istekleri, E-posta bildirimleri, SQL istekleri)

E-posta bildirimlerinin hızını artırın
---------------------------------

SMTP metodunu harici bir sunucu ile kullanmak çok yavaş olabilir.

Olası çözümler:

- SMTP'yi hala kullanmak istiyorsanız arka plan çalışma metodunu kullanın
- Postfix ile yerel bir e-posta geçişi kullanın ve "mail" aktarımı kullanın
- E-posta göndermek için bir HTTP API kullanan bir e-posta sağlayıcısı kullanın (Sendgrid, Mailgun veya Postmark)

Sqlite performansını geliştirin
---------------------------

Olası çözümler:

- Eşzamanlılık (çok sayıda kullanıcı) çok olduğunda Sqlite kullanmayın, bunun yerine Postgres veya Mysql seçin
- Sqlite'i paylaşılan bir NFS bağdaştırıcısı üzerinde kullanmayın
- Zayıf IOPS'li bir diskte Sqlite kullanmayın, yerel SSD sürücülerini kullanmak her zaman tercih edilir
