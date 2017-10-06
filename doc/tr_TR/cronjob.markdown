Arka Plan İş Zaman Planlaması
=========================

Düzgün çalışabilmek için, Kanboard günlük olarak arka planda bir iş yürütülmesini ister.
Genellikle Unix platformlarında bu işlem `cron` tarafından yapılır.

Bu arka plan işi, bu özellikler için gereklidir:

- Raporlar ve analizler (her bir projenin günlük istatistiklerini hesaplayın)
- Vazgeçilmiş görev bildirimleri gönder
- Olaya bağlı otomatik eylemleri yürütün "Görevler için günlük arka plan işi"

Unix ve Linux platformlarında konfigürasyon
-----------------------------------------

Unix/Linux işletim sistemlerinde bir cronjob tanımlamanın birden çok yolu vardır, bu örnek Ubuntu 14.04 içindir.
Prosedür, diğer sistemler için de benzerdir.

Web sunucusu kullanıcısının crontab'sını düzenleyin:

```bash
sudo crontab -u www-data -e
```

Günlük cronjobu sabah 08.00'de çalıştırma örneği:

```bash
0 8 * * * cd /path/to/kanboard && ./cli cronjob >/dev/null 2>&1
```

Not: Sqlite kullanıyorsanız, cronjob işleminin veritabanına yazma erişimi olmalıdır.
Genellikle, cronjob'u web sunucusu kullanıcısı altında çalıştırmak yeterlidir.

Microsoft Windows Server'da Yapılandırma
-----------------------------------------

Yinelenen görevi yapılandırmadan önce, Kanboard CLI komut dosyasını çalıştıran bir toplu iş dosyası (*.bat veya *.cmd) oluşturun.

İşte bir örnek (`C:\kanboard.bat`):

```
"C:\php\php.exe" -f "C:\inetpub\wwwroot\kanboard\cli" cronjob
```

**Kurulumunuza göre PHP yürütülebilir dosyanın yolunu ve Kanboard'un komut dosyasının yolunu değiştirmelisiniz.**

Windows Görev Zamanlayıcısını yapılandırın:

1. "Yönetimsel Araçlar" bölümüne gidin.
2. "Görev Zamanlayıcısı" nı açın.
3. Sağda "Görev Oluştur" u seçin
4. Bir isim seçin, örneğin "Kanboard"
5. "Güvenlik Seçenekleri" altında, Sqlite kullanıyorsanız, veritabanına yazabilecek bir kullanıcı seçin (yapılandırmanıza bağlı olarak IIS_IUSRS olabilir)
6. Yeni bir "Tetikleyici" oluşturun, günlük ve bir gece, örneğin gece vakti seçin
7. Yeni bir eylem ekleyin, "Bir programı başlat" ı seçin ve yukarıda oluşturulan toplu iş dosyasını seçin

