FROM fguillot/alpine-nginx-php7

COPY . /var/www/app
COPY docker/kanboard/config.php /var/www/app/config.php
COPY docker/crontab/cronjob.alpine /var/spool/cron/crontabs/nginx
COPY docker/services.d/cron /etc/services.d/cron
COPY docker/php/env.conf /etc/php7/php-fpm.d/env.conf

RUN chown -R nginx:nginx /var/www/app/data /var/www/app/plugins

VOLUME /var/www/app/data
VOLUME /var/www/app/plugins
