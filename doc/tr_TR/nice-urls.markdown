URL yeniden yazımı-rewriting
=============

Kanboard, URL yeniden yazarlığı etkin veya olmasın kayıtsız çalışabilir.

- URL yeniden yazılmış örneği: `/board/123`
- Aksi takdirde: `?controller=board&action=show&project_id=123`

Kanboard'u Apache ile kullanıyorsanız ve mod yeniden yazma özelliği etkinse, güzel URL'ler otomatik olarak kullanılacaktır.
"404 Bulunamadı" mesajı alırsanız, .htaccess dosyalarını çalıştırmak için DocumentRoot için en azından şu geçersiz kılmaları ayarlamanız gerekebilir:

```sh
<Directory /var/www/kanboard/>
	AllowOverride FileInfo Options=All,MultiViews AuthConfig
</Directory>
```

URL Kısayolları
-------------

- Görev "#123"'e gidin: **/t/123**
- "#2" numaralı proje panosuna gidin: **/b/2**
- Proje takvimi "#5"'e gidin: **/c/5**
- "#8." Projenin liste görünümüne gidin : **/l/8**
- Proje numarası  "#42" için proje ayarlarına gidin: **/p/42**

Yapılandırma
-------------

Varsayılan olarak, Kanboard, Apache modu yeniden yazmanın etkin olup olmadığını kontrol edecektir.

URL yeniden yazmanın web sunucusundan otomatik olarak algılanmasını önlemek için, bu özelliği yapılandırma dosyanızdan etkinleştirebilirsiniz:

```php
define('ENABLE_URL_REWRITE', true);
```

Bu sabit `true` olduğunda:

- Komut satırı araçlarından üretilen URL'ler de dönüştürülecek
- Apache'den başka bir web sunucusu, örnegin Nginx veya Microsoft IIS kullanıyorsanız, kendinize URL yeniden yazmayı yapılandırmanız gerekir

Not: Kanboard her zaman eski okul URL'lerine geçmez, yapılandırılmadığında bu yapılandırma isteğe bağlıdır.

Nginx yapılandırma örneği
---------------------------

Nginx yapılandırma dosyanızın `server` bölümünde şu örneği kullanabilirsiniz:

```bash
index index.php;

location / {
    try_files $uri $uri/ /index.php$is_args$args;

    # If Kanboard is under a subfolder
    # try_files $uri $uri/ /kanboard/index.php;
}

location ~ \.php$ {
    try_files $uri =404;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_pass unix:/var/run/php5-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_index index.php;
    include fastcgi_params;
}

# Deny access to the directory data
location ~* /data {
    deny all;
    return 404;
}

# Deny access to .htaccess
location ~ /\.ht {
    deny all;
    return 404;
}
```

Kanboard `config.php` de:

```php
define('ENABLE_URL_REWRITE', true);
```

Yukarıdaki örneği kendi yapılandırmanıza göre uyarlayın.

IIS yapılandırma örneği
-------------------------

1. IIS için Yeniden Yazma modülünü indirin ve yükleyin: [İndirme linki](http://www.iis.net/learn/extensions/url-rewrite-module/using-the-url-rewrite-module)
2. Yükleme klasörünüzde bir web.config dosyası oluşturun:

```xml
<?xml version="1.0"?>
<configuration>
    <system.webServer>
        <defaultDocument>
            <files>
                <clear />
                <add value="index.php" />
            </files>
        </defaultDocument>
        <rewrite>
            <rules>
                <rule name="Kanboard URL Rewrite" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAll">
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php" appendQueryString="true" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```

Kanboard `config.php` de:

```php
define('ENABLE_URL_REWRITE', true);
```

Yukarıdaki örneği kendi yapılandırmanıza göre uyarlayın.


