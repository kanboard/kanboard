Analizler
=========

Her projenin bir analiz bölümü vardır. Kanboard'u nasıl kullandığınıza bağlı olarak, şu raporları görebilirsiniz:

Kullanıcı yeniden bölümlendirme
----------------

![User repartition](screenshots/user-repartition.png)

Bu pasta grafik kullanıcı başına atanan açık görev sayısını gösterir.

Görev dağıtımı
-----------------

![Task distribution](screenshots/task-distribution.png)

Bu pasta grafiği, sütun başına açık görev sayısına genel bir bakış sunar.

Kümülatif akış diyagramı
-----------------------

![Cumulative flow diagram](screenshots/cfd.png)

- Bu grafik, zaman içindeki her bir sütun için toplu olarak görev sayısını gösterir.
- Her gün, her bir sütun için toplam görev sayısı kaydedilir.
- Kapatılan görevleri hariç tutmak istiyorsanız [global proje ayarları](project-configuration.markdown) seçeneğini değiştirin.

Not: Grafiği görmek için en az iki güne kadar veri içermeniz gerekir.

Geribildirim (Burndown) tablosu
---------------

![Burndown chart](screenshots/burndown-chart.png)

Her proje için [geribildirim (Burndown) tablosu] (http://en.wikipedia.org/wiki/Burn_down_chart) mevcuttur.

- Bu çizelge, zamana karşı yapılan işin grafik bir temsilidir.
- Kanboard, bu diyagramı oluşturmak için karmaşıklığı veya öykü(olay-hikaye) noktasını kullanır.
- Her gün, her sütunun öykü(olay-hikaye) puanlarının toplamı hesaplanır.

Her sütuna harcanan ortalama süre
-----------------------------------

![Average time spent into each column](screenshots/average-time-spent-into-each-column.png)

Bu grafik, son 1000 görev için her bir sütuna harcanan ortalama süreyi gösterir.

- Kanboard, veriyi hesaplamak için görev geçişlerini kullanır.
- Harcanan zaman, görev kapanıncaya kadar hesaplanır.

Ortalama Teslim ve Döngü Süresi
---------------------------

![Average time spent into each column](screenshots/average-lead-cycle-time.png)

Bu grafik, son 1000 görevin zaman içindeki ortalama teslim ve döngü süresini göstermektedir.

- Teslim zamanı, görev yaratma ve tamamlanma tarihi arasındaki zamandır.
- Döngü süresi, görevin belirtilen başlangıç tarihi ile bitiş tarihine kadar olan zamandır.
- Görev kapalı değilse, tamamlanma tarihi yerine geçerli saat kullanılır.

Bu metrikler, tüm proje için her gün hesaplanır ve kaydedilir.

Not: Doğru istatistikleri elde etmek için [günlük-daily cronjob](cronjob.markdown) çalıştırmayı unutmayın.

