Docker ile Kanboard nasıl çalıştırılır?
================================

Kanboard, [Docker](https://www.docker.com) ile kolayca çalıştırabilir.

Disk görüntü-image boyutu yaklaşık **70MB** olup aşağıdakileri içerir:

- [Alpine Linux](http://alpinelinux.org/)
- [Süreç yöneticisi S6](http://skarnet.org/software/s6/)
- Nginx
- PHP 7

Kanboard cronjob'u her gece yarısı çalışıyor.
URL yeniden yazma, birlikte gelen yapılandırma dosyasında etkinleştirilmiştir.

Kapsayıcı-konteyner çalışırken, bellek kullanımı yaklaşık **30MB** civarındadır.

Kararlı sürümü kullanmak
----------------------

Kanboard'un en kararlı sürümünü elde etmek için **stable** etiketini kullanın:

```bash
docker pull kanboard/kanboard
docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:stable
```

Geliştirme sürümünü kullanmak (otomatik yapı)
---------------------------------------------

Depodaki her yeni taahhüt, [Docker Hub](https://registry.hub.docker.com/u/kanboard/kanboard/) üzerinde yeni bir yapı oluşturur.

```bash
docker pull kanboard/kanboard
docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:latest
```

**latest** etiketi, Kanboard'un **geliştirme versiyonudur-development version**, risk almak kendi sorumluluğunuzdadır.

Kendi Docker görüntü-image oluşturun
---------------------------

Kendi görüntü-image inızı oluşturmak için Kanboard havuzunda-repository bir `Dockerfile` var.
Kanboard havuzunda-repository klonlayın ve aşağıdaki komutu çalıştırın:

```bash
docker build -t youruser/kanboard:master .
```

veya

```bash
make docker-image
```

Bağlantı noktası 80 üzerinde arka planda kapsayıcı-konteyner çalıştırmak için:

```bash
docker run -d --name kanboard -p 80:80 -t youruser/kanboard:master
```

Cilt-Volumes
-------

Kapsayıcınıza-konyetner 2 cilt bağlayabilirsiniz:

- Veri klasörü: `/var/www/app/data`
- Eklentiler-Plugins klasörü: `/var/www/app/plugins`

[Resmi Docker belgeleri](https://docs.docker.com/engine/userguide/containers/dockervolumes/) 'da açıklandığı gibi, ana makineye bir hacim bağlamak için  `-v` parametresi-bayrağını kullanın.

Kapsayıcınızı-Konteyner Yükseltme
----------------------

- Yeni görüntü-image koy
- Eski kapsayıcı-konteyner çıkarın
- Aynı ciltlere sahip yeni bir kapsayıcı-konteyner yeniden başlat

Ortam Değişkenleri
---------------------

Ortam değişkenleri listesi [bu sayfa](env.markdown) 'da mevcuttur.

Yapılandırma dosyaları
------------

- Kapsayıcı-konteyner da zaten `/var/www/app/config.php` de bulunan özel bir yapılandırma dosyası bulunmaktadır.
- Kendi yapılandırma dosyanızı veri hacmine kaydedebilirsiniz: `/var/www/app/data/config.php`.

Kaynaklar
----------

- [Resmi Kanboard görüntü-image](https://registry.hub.docker.com/u/kanboard/kanboard/)
- [Docker belgeleri](https://docs.docker.com/)
- [Dockerfile kararlı sürümü](https://github.com/kanboard/docker)
- [Dockerfile dev sürümü](https://github.com/kanboard/kanboard/blob/master/Dockerfile)
