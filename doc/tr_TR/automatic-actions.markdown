Otomatik İşlemler
=================

Kullanıcı etkileşimini en aza indirgemek için, Kanboard otomatik işlemleri desteklemektedir.

Her otomatik işlem şu şekilde tanımlanır:

- Dinlemek için bir etkinlik
- Bu etkinlikle bağlantılı işlem
- Sonunda tanımlamak için bazı parametreler

Her projenin farklı otomatik eylemler kümesi vardır, proje girişi sayfasında yapılandırma panelinde bulunur, **Otomatik işlemler** bağlantısına tıklayın.

Yeni bir eylem ekle
----------------

**Yeni bir otomatik işlem ekle** bağlantısını tıklayın.

![Automatique action](screenshots/automatic-action-creation.png)

- Bir eylem seçin
- Sonra bir etkinlik seçin
- Ve son olarak, parametreleri tanımlayın

Kullanılabilir işlemlerin listesi
-------------------------

- Harici bir sağlayıcının yorumunu oluşturma
- Görevi sütunlar arasında taşırken yorum günlüğü ekleme
- Otomatik olarak bir renge dayalı bir kategori atama
- Kategoriyi harici bir etikete göre değiştirin
- Bağlantıya dayalı otomatik olarak bir kategori atama
- Bir kategoriyi temel alan otomatik olarak bir renk ata
- Görev belirli bir kolona taşıntığında renk ata
- Belirli bir görev bağlantısı kullanırken görev rengini değiştirme
- Belirli bir kullanıcıya renk atama
- Görevi eylemi yapan kişiye atayın
- Görevi, sütun değiştiğinde işlemi yapan kişiye atayın
- Görevi belirli bir kullanıcıya atayın
- Harçlı kişiyi harici bir kullanıcı adına göre değiştirin
- Görevi kapat
- Görevi belirli bir sütunda kapatma
- Harici bir sağlayıcıdan bir görev oluşturma
- Görevin başka bir projeye kopyalanması
- Görevini e-postayla birine gönderin
- Görevi başka bir projeye taşı
- Görevi bir kullanıcıya atandığında başka bir kolona taşıyın
- Kategori değiştirildiğinde görevi başka bir kolona taşı
- Görev sahibi silindiğinde, görevi başka bir kolona taşı
- Görev aç
- Başlangıç ​​tarihini otomatik olarak güncelle

Örnekler
--------

İşte gerçek hayatta kullanılan bazı örnekler:

### Bir görevi "Bitti" kolonuna taşıdığımda, bu görevi otomatik olarak kapat

- İşlemi seçin: **Bir görevi belirli bir sütunda kapatın**
- Etkinliği seçin: **Görevi başka bir kolona taşıyın**
- Eylem parametresini tanımlayın: **Kolon=Bitti** (hedef kolon budur)

### Bir görevi "Doğrulanacak" kolonuna taşıdığımda, bu görevi belirli bir kullanıcıya atayın

- İşlemi seçin: **Görevi belirli bir kullanıcıya atayın**
- Etkinliği seçin: **Görevi başka bir kolona taşıyın**
- Eylem parametrelerini tanımlayın: **Kolon=Doğrulanacak** ve **Kullanıcı=Bob** (Bob bizim test görevlimizdir)

### Görevi "Çalışma sürüyor" kolonuna taşıdığımda, bu görevi geçerli kullanıcıya atayın

- İşlemi seçin: **Görevi, kolon değiştiğinde işlemi yapan kişiye atayın**
- Etkinliği seçin: **Görevi başka bir kolona taşıyın**
- Eylem parametresini tanımlayın: **Kolon=Çalışma sürüyor**

### Bir görev tamamlandığında, bu görevi başka bir projeye kopyalayın

Diyelim ki "Müşteri Siparişi" ve "Üretim" olmak üzere iki projemiz var, sipariş onaylandıktan sonra onu "Üretim" projesine değiştirelim.

- İşlemi seçin: **Görevi başka bir projeye çoğaltın**
- Etkinliği seçin: **Görevi kapatma**
- Eylem parametrelerini tanımlayın: **Kolon=Doğrulanmış** ve **Proje=Üretim**

### Bir görev son kolona taşıntığında, aynı görevi başka bir projeye taşıyın

Diyelim ki iki proje "Fikirler" ve "Geliştirme" var, bir kez fikir geçerliliği onaylandıktan sonra onu "Geliştirme" projesine takas edelim.

- İşlemi seçin: **Görevi başka bir projeye taşıyın**
- Etkinliği seçin: **Görevi başka bir kolona taşıyın**
- Eylem parametrelerini tanımlayın: **Kolon=Doğrulanmış** ve **Proje=Geliştirme**

### Bob kullanıcısına otomatik olarak bir renk atamak istiyorum 

- İşlemi seçin: **Belirli bir kullanıcıya renk atayın**
- Etkinliği seçin: **Görev atayanı değişim**
- Eylem parametrelerini tanımlayın: **Renk=Yeşil** ve **Atayan=Bob**

### Tanımlanan "Özellik İsteği" kategorisine otomatik olarak bir renk atamak istiyorum

- İşlemi seçin: **Otomatik olarak bir kategoriye dayalı bir renk atayın**
- Etkinliği seçin: **Görev oluşturma veya değiştirme**
- Eylem parametrelerini tanımlayın: **Renk=Mavi** ve **Kategori=Özellik İsteği**

### Görev "Çalışma sürüyor" sütununa taşındığında başlangıç ​​tarihini otomatik olarak ayarlamak istiyorum

- İşlemi seçin: **Başlangıç ​​tarihini otomatik olarak güncelleyin**
- Etkinliği seçin: **Görevi başka bir kolona taşıyın**
- Eylem parametrelerini tanımlayın: **Kolon=Çalışma sürüyor**
