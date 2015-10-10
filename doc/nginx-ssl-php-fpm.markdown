Kanboard with Nginx, HTTPS, SPDY and PHP-FPM
============================================

This installation example will help you to have the following features:

- Latest stable nginx version
- HTTPS only with a valid certificate
- [SPDY protocol](http://en.wikipedia.org/wiki/SPDY) activated
- PHP 5.5 with php-fpm
- Recommended security parameters
- File uploads with a 10MB file size limit

This procedure is written for **Ubuntu 14.04 LTS** but it should be similar for any Linux distribution.

For this setup, we suppose that only Kanboard is installed on the server.
It can be a small virtual machine by example.

Kanboard detect automatically the utilization of HTTPS and enable some extra features:

- [HTTP Strict Transport Security](http://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security)
- [Secure Cookie Flag](http://en.wikipedia.org/wiki/HTTP_cookie#Secure_and_HttpOnly)

PHP 5.5 Installation
--------------------

```bash
sudo apt-get install php5-fpm php5-cli php5-sqlite
```

You can also install `php5-mysql` if you prefer to use Kanboard with Mysql or MariaDB.

Customize your `/etc/php5/fpm/php.ini`:

```ini
; Security settings
expose_php = Off
cgi.fix_pathinfo=0

; Log errors
error_reporting = E_ALL
display_errors = Off
log_errors = On
html_errors = Off
error_log = syslog

; File uploads
upload_max_filesize = 10M
post_max_size = 10M
```

Restart PHP background processes:

```bash
sudo service php5-fpm restart
```

Nginx Installation
------------------

We want the latest stable version of nginx to be able to use the SPDY protocol.
Hopefully, there is PPA for Ubuntu (unofficial):

```bash
sudo add-apt-repository ppa:nginx/stable
sudo apt-get install nginx
```

Generate a SSL certificate
--------------------------

We want a SSL certificate that work everywhere, not a self-signed certificate.
You can buy a cheap one at [Namecheap](http://www.namecheap.com/?aff=73824) or anywhere else.

Here the different steps to configure your certificate:

```bash
# Generate a private key
openssl genrsa -des3 -out kanboard.key 2048

# Create a key with no password for Nginx
openssl rsa -in kanboard.key -out kanboard.key.nopass

# Generate the Certificate Signing Request, enter your domain name for the field 'Common Name'
openssl req -new -key kanboard.key.nopass -out kanboard.csr

# Copy and paste the content of the CSR to the Namecheap control panel and finalize the procedure
cat kanboard.csr

# After that, you receive by email your certificate, then concat everything into a single file
cat kanboard.crt COMODORSAAddTrustCA.crt COMODORSADomainValidationSecureServerCA.crt AddTrustExternalCARoot.crt > kanboard.pem
```

Copy the certificates in a new directory:

```bash
mkdir /etc/nginx/ssl
cp kanboard.pem /etc/nginx/ssl
cp kanboard.key.nopass /etc/nginx/ssl
chmod 400 /etc/nginx/ssl/*
```

Configure Nginx
---------------

Now, we can customize our installation, start to modify the main configuration file `/etc/nginx/nginx.conf`:

```nginx
user www-data;
worker_processes auto;
pid /run/nginx.pid;

events {
    worker_connections 1024;
}

http {
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off;

    # SSL shared cache between workers
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # We disable weak protocols and ciphers
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
    ssl_prefer_server_ciphers on;
    ssl_ciphers HIGH:!SSLv2:!MEDIUM:!LOW:!EXP:!RC4:!DSS:!aNULL:@STRENGTH;

    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    # We enable the Gzip compression for some mime types
    gzip on;
    gzip_disable "msie6";
    gzip_vary on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}
```

Create a new virtual host for Kanboard `/etc/nginx/sites-available/kanboard`


```nginx
server {
    # We also enable the SPDY protocol
    listen 443 ssl spdy;

    # Our SSL certificate
    ssl on;
    ssl_certificate /etc/nginx/ssl/kanboard.pem;
    ssl_certificate_key /etc/nginx/ssl/kanboard.key.nopass;

    # You can change the default root directory here
    root /usr/share/nginx/html;

    index index.php;

    # Your domain name
    server_name localhost;

    # The maximum body size, useful for file uploads
    client_max_body_size 10M;

    location / {
        try_files $uri $uri/ =404;
    }

    error_page 404 /404.html;
    error_page 500 502 503 504 /50x.html;
    location = /50x.html {
        root /usr/share/nginx/html;
    }

    # PHP-FPM configuration
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        include fastcgi.conf;
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
}
```

Now it's time to test our setup

```bash
# Disable the default virtual host
sudo unlink /etc/nginx/sites-enabled/default

# Add our default virtual host
sudo ln -s /etc/nginx/sites-available/kanboard /etc/nginx/sites-enabled/kanboard

# Check the config file
sudo nginx -t
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful

# Restart nginx
sudo service nginx restart
```

Kanboard Installation
---------------------

You can install Kanboard in a subdirectory or not, it's up to you.

```bash
cd /usr/share/nginx/html
sudo wget http://kanboard.net/kanboard-latest.zip
sudo unzip kanboard-latest.zip
sudo chown -R www-data:www-data kanboard/data
sudo rm kanboard-latest.zip
```

Now, you should be able to use Kanboard with your web browser.
