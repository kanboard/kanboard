Json-RPC API
============

Kullanıcı ve uygulama API'sı
------------------------

İki tür API erişimi vardır:

### Uygulama API'si

- "jsonrpc" kullanıcısı ve ayarlar sayfasında belirteç-token bulunan API'ya erişim
- Tüm prosedürlere erişim
- İzin verilen izin yok
- Sunucuda kullanıcı oturumu yok
- "My ..." ile başlayan işlemlere erişim yok (örneğin: "getMe" veya "getMyProjects")
- Olası hizmetlere-client örnek: veri taşıma / içe aktarma, başka bir sistemden görevler oluşturma, vb.

### Kullanıcı API'si

- Kullanıcı kimlik bilgileri (kullanıcı adı ve şifre) ile API'ye erişim
- Ayrıca, şifreniz yerine bir kişisel erişim belirteci-token da oluşturabilirsiniz.
- Uygulama rolü ve proje izinleri her prosedür için kontrol edilir
- Sunucuda bir kullanıcı oturumu oluşturuldu
- Olası hizmetlere-client örnek: yerel mobil / masaüstü uygulaması, komut satırı yardımcı programı, vb.

Güvenlik
--------

- Her zaman geçerli bir sertifika ile HTTPS kullanın (düz metin iletişimi önlemek için)
- Mobil bir uygulama yaparsanız, cihazdaki kullanıcı kimlik bilgilerini güvenli bir şekilde depolamanız sizin sorumluluğunuzdur
- Kullanıcı API'sinde 3 kimlik doğrulama hatası yapıldıktan sonra, son kullanıcı end-user giriş formunu kullanarak hesabının kilidini açması gerekir
- İki-kademeli kimlik doğrulama API aracılığıyla henüz mevcut değil

Protokol
--------

Kanboard, harici programlarla etkileşim kurmak için Json-RPC protokolünü kullanır.

JSON-RPC, JSON'da kodlanmış uzaktan yordam çağrı protokolüdür.
XML-RPC ile hemen hemen aynı şey ama JSON biçimi iledir.

[Protokolün 2. versiyonunu](http://www.jsonrpc.org/specification) kullanıyorsanız,
API'yi `POST` HTTP isteği ile çağırmalısınız.

Kanboard, yığın isteklerini destekler, böylece tek bir HTTP isteğinde birden fazla API çağrısı yapabilirsiniz. Daha yüksek ağ gecikmeli mobil istemciler için özellikle yararlıdır.

Kullanımı
-----

- [Kimlik Doğrulama](api-authentication.markdown)
- [Örnekler](api-examples.markdown)
- [Uygulama](api-application-procedures.markdown)
- [Projeler](api-project-procedures.markdown)
- [Proje İzinleri](api-project-permission-procedures.markdown)
- [Panolar](api-board-procedures.markdown)
- [Kolonlar](api-column-procedures.markdown)
- [Kulvarlar](api-swimlane-procedures.markdown)
- [Kategoriler](api-category-procedures.markdown)
- [Otomatik İşlemler](api-action-procedures.markdown)
- [Görevler](api-task-procedures.markdown)
- [Alt-Görevler](api-subtask-procedures.markdown)
- [Alt Görev Süre Çizelgesi](api-subtask-time-tracking-procedures.markdown)
- [Görev Dosyaları](api-task-file-procedures.markdown)
- [Proje Dosyaları](api-project-file-procedures.markdown)
- [Bağlantılar](api-link-procedures.markdown)
- [İç Görev Bağlantıları](api-internal-task-link-procedures.markdown)
- [Harici Görev Bağlantıları](api-external-task-link-procedures.markdown)
- [Yorumlar](api-comment-procedures.markdown)
- [Kullanıcılar](api-user-procedures.markdown)
- [Gruplar](api-group-procedures.markdown)
- [Grup Üyeleri](api-group-member-procedures.markdown)
- [Ben](api-me-procedures.markdown)
