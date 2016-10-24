Installation de Kanboard sur CentOS
===================================

CentOS 7
--------

Installez PHP et Apache :

```bash
yum install -y php php-mbstring php-pdo php-gd unzip wget
```

Par défaut, Centos 7 utilise PHP 5.4.16 et Apache 2.4.6.

Redémarrer Apache :

```bash
systemctl restart httpd.service
```

Installez Kanboard :

```bash
cd /var/www/html
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R apache:apache kanboard/data
rm kanboard-latest.zip
```

CentOS 6.x
----------

Installez PHP et Apache :

```bash
yum install -y php php-mbstring php-pdo php-gd unzip wget
```

Par défaut, Centos 6.5 utilise PHP 5.3.3 et Apache 2.2.15.

Activez les "short tags":

- Modifiez le fichier `/etc/php.ini`
- Changez cette ligne pour que la valeur soit à `On` : `short_open_tag = On`

Redémarrez Apache:

```bash
service httpd restart
```

Installez Kanboard :

```bash
cd /var/www/html
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R apache:apache kanboard/data
rm kanboard-latest.zip
```

Restrictions SELinux
--------------------

Si SELinux est activé, soyez sûr que Apache puisse écrire dans le répertoire `data` :

```bash
chcon -R -t httpd_sys_content_rw_t /var/www/html/kanboard/data
```

La même chose s'applique pour autoriser Kanboard à envoyer des requêtes réseau externes :

```bash
setsebool -P httpd_can_network_connect=1
```

Autoriser des connexions externes est nécessaire si vous souhaitez utiliser LDAP, SMTP ou les webhooks.

Notes
-----

Certaines fonctionnalités de Kanboard demandent à ce que vous installiez une [tâche planifiée](cronjob.markdown).
