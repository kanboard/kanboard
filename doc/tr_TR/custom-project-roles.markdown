Özel Proje Rolleri
====================

Bu role ait kişiler üzerinde belirli kısıtlamalar dizisi uygulamak için özel proje rolleri oluşturabilirsiniz.
Bu özel roller her proje için tanımlanmıştır.

Özel rol, proje üyesi rolünden devralır.
Örneğin, birini bir işlemi takip etmeye zorlamak için özel bir rol oluşturmak isteyebilirsiniz.
Görevleri yalnızca "Devam etmekte olan iş" sütunundan "Bitti" sütununa taşımanıza izin verilen bir grup insana sahip olabilirsiniz.

Mevcut kısıtlamalar
----------------------

- Proje Kısıtlamaları:
    - Görev oluşturulmasına izin verilmiyor
    - Bir görevi kapatmak veya açmak yasaktır
    - Görevin taşınmasına izin verilmiyor
- Sütun Kısıtlamaları:
    - Görev oluşturulması sadece belirli bir sütun için **izin** verilir
    - Görev oluşturulması yalnızca belirli bir sütun için **engel** lenir
    - Bir görevi kapatmak veya açmak için sadece belirli bir sütuna **izin** verilir
    - Bir görevi kapatmak veya açmak için yalnızca belirli bir sütun için **engel** lenir
- Görevleri yalnızca belirtilen sütunlar arasında taşıma

Yapılandırma
-------------

### 1) Yeni bir özel rol oluştur

Proje ayarlarından, **Özel Roller** menüsünde soldaki simgesini tıklayın ve sayfanın üst kısmında **Yeni özel rol ekleyin** seçeneğini tıklayın.
 
![New custom role](screenshots/new_custom_role.png)

Rol için bir isim verin ve formu gönderin.

### 2) Rol için bir sınırlama ekleyin

Burada farklı kısıtlamalar vardır:

- Proje kısıtlamaları
- Sürükle ve bırak kısıtlamaları
- Sütun kısıtlamaları

Yeni bir kısıtlama eklemek için tabloda açılır menüye tıklayabilirsiniz:

![Add a new restriction](screenshots/add_new_restriction.png)

### 3) Kısıtlamalar listesi

![List of restrictions](screenshots/example-restrictions.png)

Örneğin, bu rol yalnızca "Geri Kayıt-Backlog" sütununda görevler oluşturabilir ve görevleri "Hazır" ve "Devam etmekte olan" sütunları arasında taşımak mümkündür.

### 4) Rolü birine atayın

Sol menüdeki "izinler" bölümüne gidin ve istenen rolü kullanıcıya atayın. 

![Custom project role](screenshots/custom_roles.png)

Örnekler
--------

### Kullanıcıların yalnızca belirli sütunlarda görev oluşturmasına izin ver

![Example restriction task creation](screenshots/example-restriction-task-creation.png)

- Bu role ait kullanıcılar, yalnızca "Geri Kayıt-Backlog" sütununda yeni görevler oluşturabilir.
- 2 kuralın kombinasyonu önemlidir, aksi takdirde bu işe yaramaz.

### Kullanıcıların görev durumunu yalnızca belirli sütunlarda değiştirmelerine izin ver

![Example restriction task status](screenshots/example-restriction-task-status.png)

- Bu role ait olan kullanıcılar, "Geri Kayıt-Backlog" sütunundaki görev durumunu değiştirebilecek.
- Durum açık olan görevler tahta üzerinde görünür ve durum kapalı olan görevler varsayılan olarak tahtada gizlidir.

### Kullanıcıların belirli bir sütundaki görev durumunu değiştirmesine izin verme

![Example column restriction](screenshots/example-restriction-task-status-blocked.png)

Bu role ait kullanıcılar, "Tamamlandı" sütunundaki görev durumunu değiştiremez.
Ancak diğer sütunlarda da mümkün olacaktır.

### Kullanıcıların görevleri yalnızca belirli sütunlar arasında taşımasına izin ver

![Example restriction task drag and drop](screenshots/example-restriction-task-drag-and-drop.png)

Bu role ait kullanıcılar, görevleri yalnızca "Hazır" ve "Devam etmekte olan" sütunları arasında taşıyabilir.
