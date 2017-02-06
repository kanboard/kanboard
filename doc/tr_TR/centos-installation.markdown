CentOS'da Kanboard Kurulumu
===============================

Centos 7
--------

PHP'yi ve Apache'ye kurun:

```bash
yum install -y php php-mbstring php-pdo php-gd unzip wget
```

Varsayılan olarak, Centos 7, PHP 5.4.16 ve Apache 2.4.6 kullanır.

Apache'yi yeniden başlatın:

```bash
systemctl restart httpd.service
```

Kanboard'u kurmak:

```bash
cd /var/www/html
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R apache:apache kanboard/data
rm kanboard-latest.zip
```

Centos 6.x
----------

PHP'yi ve Apache'yi kurun:

```bash
yum install -y php php-mbstring php-pdo php-gd unzip wget
```

Varsayılan olarak, Centos 6.5, PHP 5.3.3 ve Apache 2.2.15'i kullanır.

Kısa etiketleri etkinleştirin:

- Dosyayı düzenleyin `/etc/php.ini`
- Satırı değiştirin; `short_open_tag = On`

Apache'yi yeniden başlatın:

```bash
service httpd restart
```

Kanboard'u kurmak:

```bash
cd /var/www/html
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R apache:apache kanboard/data
rm kanboard-latest.zip
```

SELinux kısıtlamaları
--------------------

SELinux etkinleştirilmişse, Apache kullanıcısının dizin verilerine yazabildiğinden emin olun:

```bash
chcon -R -t httpd_sys_content_rw_t /var/www/html/kanboard/data
```

Sunucunuzu, SELinux'la olduğu gibi, Kanboard'un e-posta göndermelerine ve harici ağ istekleri almasına izin verecek şekilde yapılandırmalarını sağlayın:

```bash
setsebool -P httpd_can_network_connect=1
```

LDAP, SMTP, Web kancaları veya herhangi bir üçüncü taraf entegrasyonu kullanıyorsanız harici bağlantılara izin verilmesi gereklidir.

Notlar
-----

Kanboard'un bazı özellikleri, [günlük arka plan işleri](cronjob.markdown)  çalıştırmanızı gerektirir.
