FROM alpine:3.15.4

VOLUME /var/www/app/data
VOLUME /var/www/app/plugins
VOLUME /etc/nginx/ssl

EXPOSE 80 443

ARG VERSION

RUN apk --no-cache --update add \
    tzdata openssl unzip nginx bash ca-certificates s6 curl ssmtp mailx php8 php8-phar php8-curl \
    php8-fpm php8-json php8-zlib php8-xml php8-dom php8-ctype php8-opcache php8-zip php8-iconv \
    php8-pdo php8-pdo_mysql php8-pdo_sqlite php8-pdo_pgsql php8-mbstring php8-session php8-bcmath \
    php8-gd php8-openssl php8-sockets php8-posix php8-ldap php8-simplexml && \
    rm -rf /var/www/localhost && \
    rm -f /etc/php8/php-fpm.d/www.conf && \
    ln -s /usr/bin/php8 /usr/bin/php

ADD . /var/www/app
ADD docker/ /

RUN rm -rf /var/www/app/docker && echo $VERSION > /version.txt

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD []
