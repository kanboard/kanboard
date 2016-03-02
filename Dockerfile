FROM gliderlabs/alpine:latest
MAINTAINER Frederic Guillot <fred@kanboard.net>

RUN apk-install nginx bash ca-certificates s6 curl \
    php-fpm php-json php-zlib php-xml php-dom php-ctype php-opcache php-zip \
    php-pdo php-pdo_mysql php-pdo_sqlite php-pdo_pgsql php-ldap \
    php-gd php-mcrypt php-openssl php-phar \
    && curl -sS https://getcomposer.org/installer | php -- --filename=/usr/local/bin/composer

RUN cd /var/www \
    && curl -LO https://github.com/fguillot/kanboard/archive/master.zip \
    && unzip -qq master.zip \
    && rm -f *.zip \
    && mv kanboard-master kanboard \
    && cd /var/www/kanboard && composer --prefer-dist --no-dev --optimize-autoloader --quiet install \
    && chown -R nginx:nginx /var/www/kanboard \
    && chown -R nginx:nginx /var/lib/nginx

COPY .docker/services.d /etc/services.d
COPY .docker/php/conf.d/local.ini /etc/php/conf.d/
COPY .docker/php/php-fpm.conf /etc/php/
COPY .docker/nginx/nginx.conf /etc/nginx/
COPY .docker/kanboard/config.php /var/www/kanboard/
COPY .docker/kanboard/config.php /var/www/kanboard/
COPY .docker/crontab/kanboard /var/spool/cron/crontabs/nginx

EXPOSE 80

VOLUME /var/www/kanboard/data
VOLUME /var/www/kanboard/plugins

ENTRYPOINT ["/bin/s6-svscan", "/etc/services.d"]
CMD []
