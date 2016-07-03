FROM fguillot/alpine-nginx-php7

COPY . /var/www/app
COPY docker/kanboard/config.php /var/www/app/config.php
COPY docker/crontab/cronjob.alpine /var/spool/cron/crontabs/nginx
COPY docker/services.d/cron /etc/services.d/cron

RUN cd /var/www/app && composer --prefer-dist --no-dev --optimize-autoloader --quiet install
RUN chown -R nginx:nginx /var/www/app/data /var/www/app/plugins

VOLUME /var/www/kanboard/data
VOLUME /var/www/kanboard/plugins
