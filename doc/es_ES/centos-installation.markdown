Instalación en Centos
===================

Nota:Algunas funciones de Kanboard requieren que se ejecute un trabajo en segundo
plano todos los días (cronjob.markdown).

Centos 7
--------

Instalar PHP y Apache:

```bash
yum install -y php php-mbstring php-pdo php-gd unzip wget
```

Por default Centos 7 use PHP 5.4.16 and Apache 2.4.6.

Reiniciar Apache:

```bash
systemctl restart httpd.service
```

InstalarKanboard:

```bash
cd /var/www/html
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R apache:apache kanboard/data
rm kanboard-latest.zip
```

Si SELinux esta activado, Asegurese de que el usuario de Apache pueda escribir en el directorio de datos:

```bash
chcon -R -t httpd_sys_content_rw_t /var/www/html/kanboard/data
```

Asegurese de configurar el servidor para permitir enviar correos electrónicos y hacer conexiones externas por ejemplo con SELinux:

```bash
setsebool -P httpd_can_network_connect=1
```

Las conexiones son necesarias si se usa LDAP,SMTP, ganchos web o cualquier integracion de terceros:

Centos 6.x
----------

Instalar PHP y Apache:

```bash
yum install -y php php-mbstring php-pdo php-gd unzip wget
```

Por defecto para Centos 6.5 use PHP 5.3.3 y Apache 2.2.15

Active las etiquetas cortas:

- Editar el archivo `/etc/php.ini`
- Cambiar la linea `short_open_tag = On`

Reiniciar Apache:

```bash
service httpd restart
```

Instalar Kanboard:

```bash
cd /var/www/html
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R apache:apache kanboard/data
rm kanboard-latest.zip
```

Ir a  `http://your_server/kanboard/`.
