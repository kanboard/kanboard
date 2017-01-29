Veritabanlar Arası Transfer Sorunlarını Çözme
=================================

- Kanboard'u yeni bir sürüme yükselttiğinizde, SQL transferleri(migrations) otomatik olarak yürütülür
- Postgres ve MySQL için geçerli şema sürüm numarası `schema_version` tablosunda saklanır ve Sqlite için bu değişken ` user_version` değişkeninde saklanır
- Transfer(migrations) dosyaları `app/Schema/<DatabaseType>.php` dosyasında tanımlanır
- Her işlev bir transfer işlemidir
- Her transfer, bir işlemde yürütülür
- Transfer işleminde bir hata oluşturursa geri alma gerçekleştirilir

Yeni sürüme geçerken:

- Daima verilerinizi yedekleyin
- Transfer işlemlerini birden çok işlemden paralel olarak çalıştırmayın

"SQL geçişleri çalıştırılamadı [...]" hatası alıyorsanız, el ile düzeltme adımları şunlardır:

1. Veritabanınıza karşılık gelen dosyayı açın `app/Schema/Sqlite.php` veya `app/Schema/Mysql.php`
2. Başarısız transfer işlevine gidin
3. İşlevde tanımlanan SQL sorgularını manuel olarak çalıştırın
4. Bir hata ile karşılaşırsanız, sorunu tam hata ile birlikte hata izleyicisine bildirin
5. Transferin tüm SQL deyimleri yürütüldüğünde, şema sürüm numarasını güncelleyin
6. Diğer transfer işlemlerini çalıştırın
