FROM ubuntu:16.04

RUN mkdir -p /var/lock/apache2 /var/run/apache2 /var/log/supervisor

RUN apt-get update -qq && \
    apt-get install -y apache2 supervisor cron curl unzip \
    libapache2-mod-php7.0 php7.0-cli php7.0-mbstring php7.0-xml php7.0-mysql php7.0-sqlite3 \
    php7.0-opcache php7.0-json php7.0-pgsql php7.0-ldap php7.0-gd php7.0-zip && \
    apt clean && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf && \
    a2enmod rewrite && \
    curl -sS https://getcomposer.org/installer | php -- --filename=/usr/local/bin/composer

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html/data /var/www/html/plugins

COPY tests/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY tests/configs /configs/

EXPOSE 80

ENTRYPOINT ["/var/www/html/tests/docker/entrypoint.sh"]
