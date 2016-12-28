Translations
============

Kanboard'u yeni bir dilde nasıl tercüme edebilirim?
--------------------------------------------

- Çeviriler, `app/Locale` dizininde saklanır
- Her dil için bir alt dizin var; örneğin Fransızca'da `fr_FR`, İtalyanca'da `it_IT`, Türkçe'de `tr_TR` vb.
- Bir çeviri, bir anahtar-değer çifti içeren bir dizi-Array döndüren bir PHP dosyasıdır
- Anahtar, İngilizce orijinal metindir ve değer ilgili dildeki tercümedir
- **Fransızca çeviriler her zaman günceldir**
- Daima son sürümü kullanın (branch master)

### Yeni çeviri oluştur:

1. Yeni bir dizin yapın: `app/Locale/xx_XX`  örneğin Kanada Fransızcası için `app/Locale/fr_CA`
2. Çeviri için yeni bir dosya oluşturun: `app/Locale/xx_XX/translations.php`
3. Fransızca yerel ayarların içeriğini kullanın ve değerleri değiştirin
4. `app/Model/Language.php` dosyasını güncelleyin
5. Her şey yolunda giderse, Kanboard'u yerel olarak kurun.
6. [Github ile çekme isteği-pull-request with Github](https://help.github.com/articles/using-pull-requests/) gönderin

Mevcut bir çeviri nasıl güncellenir?
--------------------------------------

1. Çeviri dosyasını açın `app/Locale/xx_XX/translations.php`
2. Kayıp çeviriler `//` ile yorumlanır ve değerler boş, sadece boşluk doldurun ve açıklamayı kaldırın.
3. Yerel kurulumunuz olan Kanboard'u kontrol edin ve bir [pull-request](https://help.github.com/articles/using-pull-requests/) gönderin.

Uygulamaya yeni çevrilmiş metin nasıl eklenir?
--------------------------------------------------

Çeviriler, kaynak kodunda aşağıdaki işlevlerle birlikte görüntülenir:

- `t()`: HTML escaping-çıkışı olan metinleri görüntüle
- `e()`: HTML escaping-çıkışı olmadan metinleri görüntüle

Kaynak kodunda daima İngilizce sürümünü kullanın.

Metin dizeleri, öğeleri değiştirmek için `sprintf()` işlevini kullanır:

- `%s` bir karakter-kelime-string yerine kullanılır
- `%d` bir tamsayıyı-integer değiştirmek için kullanılır

Tüm formatlar için [PHP Belgeleri-documentation](http://php.net/sprintf).

Uygulamalardaki eksik çevirileri nasıl bulabilirim?
-----------------------------------------------------

Bir terminalden aşağıdaki komutu çalıştırın:

```bash
./cli locale:compare
```

Eksik ve kullanılmayan tüm çeviriler ekranda görüntülenir.
Bunu Fransız yerel ayarına koyun ve diğer yerel ayarları eşzamanlayın (aşağıya bakın).

Çeviri dosyalarını nasıl senkronize ederim?
-------------------------------------

Bir Unix shell-kabuğundan şu komutu çalıştırın:

```bash
./cli locale:sync
```

Fransızca çevirisi diğer yerel ayarlarda referans olarak kullanılır.
