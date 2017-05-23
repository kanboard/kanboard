Gelişmiş Arama Sözdizimi(Kodları)
======================

Kanboard, gelişmiş arama için basit bir sorgu dili kullanıyor.
Görevler, yorumlar, alt görevler, bağlantılar ile aynı zamanda etkinlik akışında da arama yapabilirsiniz.

Sorgu örneği
----------------

Bu örnek, yarın için bir bitiş tarihi ve "başlığım" ı içeren bir başlık ile bana atanan tüm görevleri geri alacaktır:

```
assigne:me due:tomorrow başlığım
```

Genel arama
-------------

### Görev kimliği veya başlığa göre arama

- Görev kimliği ile ara: `#123`
- Görev kimliği ve görev başlığına göre ara: `123`
- Görev başlığına göre ara: herhangi bir arama nitelikleriyle eşleşmeyen herhangi bir şey

### Duruma göre ara

Özellik: **status**

- Açık görevleri bulmak için sorgu: `status:open`
- Kapatılan görevleri bulmak için sorgu: `status:closed`

### Devralan göre ara

Özellik: **assignee**

- Tam adıyla sorgu: `assignee:"Frederic Guillot"`
- Kullanıcı adı ile sorgu: `assignee:fguillot`
- Birden fazla atanan arama: 'assignee:user1 assignee:"John Doe"
- Atanmamış görevler için sorgu: 'assignee:nobody'
- Görevlerimin sorgulanması: 'assignee:me`

### Görev yaratıcısına göre ara

Özellik: **creator**

- Benim tarafından oluşturulan görevler: `creator:me`
- John Doe tarafından oluşturulan görevler: `creator:"John Doe"`
- Kullanıcı no #1 tarafından oluşturulan görevler: `creator:1`

### Alt görev atayan tarafından arama yapın

Özellik: **subtask:assignee**

- Örnek: `subtask:assignee:"John Doe"`

### Renk ile ara

Özellik: **color**

- Renk kimliği ile arama yapmak için sorgu: `color:mavi`
- Renk adına göre arama yapmak için sorgu: `color:"Oranj"`

### Vadesine göre ara

Özellik: **due**

- Bugünkü görevler için arama yapın: `due:today`
- Yarınki görevler için arama yapın: `due:tomorrow`
- Dünkü görevler için arama yapın: `due:yesterday`
- Tam tarihi olan görevlerde arama yapın: `due:2015-06-29`

Tarihin ISO 8601 biçimi ile kullanması gerekir: **YYYY-MM-DD**.

`strtotime ()` işlevi tarafından desteklenen tüm dize formatları desteklenmektedir, örneğin `next Thursday`,` -2 days`, `+2 months`, `tomorrow`, vb.

Tarih ile desteklenen operatörler:

- Bundan büyük: **due:>2015-06-29**
- Bundan küçük: **due:<2015-06-29**
- Bundan büyük veya eşit: **due:>=2015-06-29**
- Bundan küçük veya eşit: **due:<=2015-06-29**

### Değiştirilme tarihine göre ara

Özellik: **modified** or **updated**

Tarih biçimleri son tarihle aynıdır.

Yakın zamanda değiştirilmiş görevlerde aynı zamanda bir filtre var:: `modified:recently`.

Bu sorgu, ayarlarda yapılandırılan pano vurgulama dönemiyle aynı değeri kullanacaktır.

### Oluşturma tarihine göre ara

Özellik: **created**

Değiştirme tarihi sorguları aynı şekilde çalışır.

### Başlangıç tarihine göre ara

Özellik: **started**

### Açıklamaya göre ara

Özellik: **description** veya **desc**

Örnek: `description:"metin arama"`

### Dış referansa göre ara

Görev referansı, görevinizin harici bir kimliği, örneğin başka bir yazılımdan gelen bir bilet numarasıdır.

- Görevleri referans ile bulun: `ref:1234` veya `reference:TICKET-1234`
- Wildcard search: `ref:TICKET-*`

### Kategoriye göre ara

Özellik: **category**

- Görevleri belirli bir kategori ile bulun: `category:"Feature Request"`
- Bu kategorilere sahip tüm görevleri bulun: `category:"Bug" category:"İyileştirmeler"`
- Hiçbir kategori atanmamış görevler bulun: `category:none`

### Projeye göre ara

Özellik: **project**

- Görevleri proje adına göre bulun: `project:"Benim proje adım"`
- Görevleri proje idine göre bulun: `project:23`
- Çeşitli projeler için görevler bulun: `project:"Benim projem A" project:"Benim projem B"`

### Sütunlara göre ara

Özellik: **column**

- Görevleri sütun adına göre bul: `column:" Devam eden işler"`
- Birkaç sütun için görevler bulun: `column:"Backlog" column:hazır`

### Kulvar (Swim-lane) lara göre ara

Özellik: **swimlane**

- Görevleri kulvarlara(swim-lane) göre ara: `swimlane:"Version 42"`
- Çeşitli kulvarlar (swim-lanes) için görev ara: `swimlane:"Version 1.2" swimlane:"Version 1.3"`

### Görev bağlantısı ile arama

Özellik: **link**

- Görevleri bağlantı adına göre bulma: `link:"is a milestone of"`
- Görevleri birkaç bağlantıya bul: `link:"is a milestone of" link:"relates to"`

### Yorumlara göre ara

Özellik: **comment**

- Bu başlık içeren yorumları bulun: `comment:"Yorum mesajım"`

### Etiketlere göre ara

Özellik: **tag**

- Örnek: `tag:"Etiketim"`

Etkinlik akışı arama
----------------------

### Görev başlıklarına göre etkinlik arama

Özellik: **title** veya yok (varsayılan)

- Örnek: `title:"Benim Görevim"`
- Görev no ile ara: `#123`

### Görev durumuna göre olayları arama

Özellik: **status**

### Olay yaratıcısı tarafından arayın

Özellik: **creator**

### Olay oluşturma tarihine göre ara

Özellik: **created**

### Etkinlikleri projeye göre ara

Özellik: **project**
