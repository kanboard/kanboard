FROM alpine:3.4
MAINTAINER Frederic Guillot <fred@kanboard.net>

RUN apk update && \
    apk add nginx bash ca-certificates s6 curl \
    php5-fpm php5-json php5-zlib php5-xml php5-dom php5-ctype php5-opcache php5-zip \
    php5-pdo php5-pdo_mysql php5-pdo_sqlite php5-pdo_pgsql php5-ldap \
    php5-gd php5-mcrypt php5-openssl php5-phar && \
    rm -rf /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --filename=/usr/local/bin/composer

RUN cd /var/www \
    && curl -LO https://github.com/fguillot/kanboard/archive/master.zip \
    && unzip -qq master.zip \
    && rm -f *.zip \
    && mv kanboard-master kanboard \
    && cd /var/www/kanboard && composer --prefer-dist --no-dev --optimize-autoloader --quiet install \
    && chown -R nginx:nginx /var/www/kanboard \
    && chown -R nginx:nginx /var/lib/nginx

COPY .docker/services.d /etc/services.d
COPY .docker/php/conf.d/local.ini /etc/php5/conf.d/
COPY .docker/php/php-fpm.conf /etc/php5/
COPY .docker/nginx/nginx.conf /etc/nginx/
COPY .docker/kanboard/config.php /var/www/kanboard/
COPY .docker/kanboard/config.php /var/www/kanboard/
COPY .docker/crontab/kanboard /var/spool/cron/crontabs/nginx

EXPOSE 80

VOLUME /var/www/kanboard/data
VOLUME /var/www/kanboard/plugins

ENTRYPOINT ["/bin/s6-svscan", "/etc/services.d"]
CMD []
