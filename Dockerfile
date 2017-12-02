FROM alpine:3.7

VOLUME /var/www/app/data
VOLUME /var/www/app/plugins

EXPOSE 80

ARG VERSION

RUN apk update && \
    apk add unzip nginx bash ca-certificates s6 curl ssmtp mailx php7 php7-phar php7-curl \
    php7-fpm php7-json php7-zlib php7-xml php7-dom php7-ctype php7-opcache php7-zip php7-iconv \
    php7-pdo php7-pdo_mysql php7-pdo_sqlite php7-pdo_pgsql php7-mbstring php7-session \
    php7-gd php7-mcrypt php7-openssl php7-sockets php7-posix php7-ldap php7-simplexml && \
    rm -rf /var/cache/apk/* && \
    rm -rf /var/www/localhost && \
    rm -f /etc/php7/php-fpm.d/www.conf

RUN cd /tmp \
    && curl -sL -o kb.zip https://github.com/kanboard/kanboard/archive/$VERSION.zip \
    && unzip -qq kb.zip \
    && cd kanboard-* \
    && cp -R . /var/www/app \
    && cd /tmp \
    && rm -rf /tmp/kanboard-* /tmp/*.zip

ADD docker/ /

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD []
