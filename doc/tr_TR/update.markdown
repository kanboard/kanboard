Kanboard'u yeni bir sürüme güncelleyin
=================================

Çoğu zaman, Kanboard'un daha yeni bir sürümüne yükseltilmesi sorunsuzdur.
İşlem, yalnızca mevcut veri klasörünüzü yeni Kanboard klasörüne kopyalamak olarak özetlenebilir.
Kanboard otomatik olarak veritabanı geçişlerini-transferlerini sizin için yapar.

Güncellemeden önce yapılması gereken önemli şeyler
--------------------------------------

- **Yeni sürüme geçmeden önce verilerinizin her zaman yedek alın**
- **Yedeklemenizin geçerli olup olmadığını kontrol edin!**
- Tekrar kontrol edin
- Değişikliklerin olup olmadığını kontrol etmek için daima [değişiklik geçmişini](https://github.com/kanboard/kanboard/blob/master/ChangeLog) okuyun
- Çalıştırıcıyı-worker  kullanıyorsanız durdurun
- Web sunucusunu bakım moduna geçirin; böylece kullanıcılar güncelleme işlemi sırasında yazılımı kullanmazlar

Arşivden (kararlı sürüm)
---------------------------------

1. Yeni arşivin sıkıştırmasını açın
2. `data` klasörünü yeni sıkıştırılması açılmış dizine kopyalayın
3. Özel ``config.php` dosyanız varsa kopyalayın.
4. Bazı eklentileri kurduysanız, en yeni sürümlerini kullanın
5. `data` dizininin web sunucusu kullanıcısı tarafından yazılabilir olduğundan emin olun
6. Test edin
7. Eski Kanboard dizininizi kaldırın

Depodan-repository (geliştirme versiyonu)
-----------------------------------------

1. `git pull`
2. `composer install --no-dev`
3. Giriş yapın ve her şeyin yolunda olduğunu kontrol edin

Not: Bu yöntem, **mevcut geliştirme sürümünü** yükleyecektir, bu versiyonu kullanmanız kendi sorumluluğunuzdadır.

SQL geçişlerini el-ile-manuel olarak çalıştırma
-------------------------------

Varsayılan olarak, SQL geçişleri otomatik olarak yürütülür. Her istekte şema sürümü kontrol edilir.
Bu şekilde, Kanboard'u başka bir sürüme yükselttiğinizde, veritabanı şeması sizin için güncellenir.

Belirli bir yapılandırmanız olması durumunda bu davranışı devre dışı bırakmak isteyebilirsiniz.
Örneğin, birden çok işlem göçleri-transferleri aynı anda uygulamayı denerseniz, her işlem bir işlem içinde yürütülseler de eşzamanlılık sorunlarınız olabilir.

Bu özelliği devre dışı bırakmak için, [config file](config.markdown) 'da `DB_RUN_MIGRATIONS` parametresini `false` olarak ayarlayın.

Kanboard'u yükseltmeniz gerektiğinde, şu komutu çalıştırın:

```bash
./cli db:migrate
```
