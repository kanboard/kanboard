How to Enable Secure Connections
================================

If you are hosting Kanboard on a public server, and plan on accessing it via the internet, it is a good idea to enable SSL connections to encrypt your data. This is very simple to do with a self-signed certificate. The following instructions were created and tested on a server running Debian 7 (Wheezy) but they should work for any other Linux distribution. 

OpenSSL Installation and Configuration
--------------------------------------
Install OpenSSL: 

```bash
apt-get update
apt-get install openssl
```
Enable OpenSSL: 

```bash 
a2enmod ssl 
```

Create a self-signed certificate: 

```bash
mkdir /etc/apache2/ssl
openssl req -new -x509 -days 365 -nodes -out /etc/apache2/ssl/kanboard.pem -keyout /etc/apache2/ssl/kanboard.key
```

Apache Configuration
--------------------
Create an apache configuration file: 

```bash
nano /etc/apache2/conf.d/kanboard.conf 
``` 

The contents of this file should look like this: 

```bash 
<VirtualHost 127.0.0.1:443>
	SSLEngine on 
	SSLCertificateFile /etc/apache2/ssl/kanboard.pem
	SSLCertificateKeyFile /etc/apache2/ssl/kanboard.key
	DocumentRoot /var/www
<Directory kanboard>
	AllowOverride All 
	order allow, deny
	Allow from all 
</Directory>
</VirtualHost>
```

Be sure to replace 127.0.0.1 with the IP address of your server. If you are hosting kanboard in a location other than /var/www , be sure to update the DocumentRoot to match the location where you are hosting your Kanboard site. 

Restart Apache: 

```bash
service apache2 restart 
``` 

You will now be able to access your Kanboard site securely by navigating to `https://www.example.com/kanboard`. Your browser will indicate that the certificate is not trusted. This is due to the fact that it is self signed. You can safely ignore this warning, although the certificate is not trusted, your data is still encrypted. 
