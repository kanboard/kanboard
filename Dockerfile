FROM alpine:3.21

LABEL org.opencontainers.image.source="https://github.com/kanboard/kanboard" \
    org.opencontainers.image.title="Kanboard" \
    org.opencontainers.image.description="Kanboard is project management software that focuses on the Kanban methodology" \
    org.opencontainers.image.vendor="Kanboard" \
    org.opencontainers.image.licenses="MIT" \
    org.opencontainers.image.url="https://kanboard.org" \
    org.opencontainers.image.documentation="https://docs.kanboard.org"

VOLUME ["/var/www/app/data", "/var/www/app/plugins", "/etc/nginx/ssl"]

EXPOSE 80 443

ARG VERSION

RUN apk --no-cache --update add \
    tzdata openssl unzip nginx bash ca-certificates s6 curl ssmtp mailx php83 php83-phar php83-curl \
    php83-fpm php83-json php83-zlib php83-xml php83-dom php83-ctype php83-opcache php83-zip php83-iconv \
    php83-pdo php83-pdo_mysql php83-pdo_sqlite php83-pdo_pgsql php83-mbstring php83-session php83-bcmath \
    php83-gd php83-openssl php83-sockets php83-posix php83-ldap php83-simplexml php83-xmlwriter && \
    rm -rf /var/www/localhost && \
    rm -f /etc/php83/php-fpm.d/www.conf && \
    ln -sf /usr/bin/php83 /usr/bin/php

ADD . /var/www/app
ADD docker/ /

RUN rm -rf /var/www/app/docker && echo $VERSION > /var/www/app/app/version.txt

HEALTHCHECK --start-period=3s --timeout=5s \
  CMD curl -f http://localhost/healthcheck.php || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD []
