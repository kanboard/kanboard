FROM fguillot/alpine-nginx-php7

COPY . /var/www/app
RUN mkdir -p /var/www/app/config
COPY docker/kanboard/config.php /var/www/app/config/config.php
COPY docker/crontab/cronjob.alpine /var/spool/cron/crontabs/nginx
COPY docker/services.d/cron /etc/services.d/cron
COPY docker/php/env.conf /etc/php7/php-fpm.d/env.conf

RUN ln -s /var/www/app/config/config.php /var/www/app/config.php && cd /var/www/app && composer --prefer-dist --no-dev --optimize-autoloader --quiet install
RUN chown -R nginx:nginx /var/www/app/data /var/www/app/plugins /var/www/app/config

VOLUME /var/www/app/data
VOLUME /var/www/app/plugins
VOLUME /var/www/app/config 