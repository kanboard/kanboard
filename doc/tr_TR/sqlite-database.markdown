Sqlite veritabanı yönetimi
==========================

Kanboard, verilerini depolamak için varsayılan olarak Sqlite kullanır.
Tüm görevler, projeler ve kullanıcılar bu veritabanında saklanır.

Teknik olarak, veritabanı `data`  dizini içinde bulunan ve `db.sqlite` olarak adlandırılan tek bir dosyadır.

Dışa Aktar/Yedekle
-------------

### Komut satırı

Yedekleme yapmak çok kolay, kimsenin yazılımı kullanmadığı zaman `data/db.sqlite` dosyasını başka bir yere kopyalamalısın.

### Kullanıcı arayüzü

Veritabanını istediğiniz zaman **ayarlar** menüsünden indirebilirsiniz.

İndirilen veritabanı Gzip ile sıkıştırılır, dosya adı `db.sqlite.gz` olur.

İthalat/Restorasyon
------------------

Veritabanını kullanıcı arabiriminden geri yüklemenin hiçbir yolu yoktur.
Restorasyon, herhangi bir vücut yazılımı kullanmadığında manuel olarak yapılmalıdır.

- Eski bir yedeklemeyi geri yüklemek için `data/db.sqlite` dosyasını değiştirin ve üzerine kaydedin.
- Sıkıştırılmış bir veritabanını açmak için, terminalde bu komutu; `gunzip db.sqlite.gz` çalıştırın.

Optimizasyon
------------

Bazen, `VACUUM` komutu çalıştırarak veritabanı dosyasını optimize etmek mümkündür.
Bu komut, tüm veritabanını yeniden oluşturur ve çeşitli nedenlerle kullanılabilir:

- Dosya boyutunu küçült, verileri silerek boş alan yarat, ancak dosya boyutunu değiştirmez.
- Veritabanı sık eklemeler veya güncellemeler nedeniyle parçalanmış.

### Komut satırından

```
sqlite3 data/db.sqlite 'VACUUM'
```

### Kullanıcı arayüzünden

**ayarlar** menüsüne gidin ve **Veritabanını optimize et** linkine tıklayın.

Daha fazla bilgi için [Sqlite belgeler](https://sqlite.org/lang_vacuum.html).


