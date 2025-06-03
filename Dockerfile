FROM alpine:3.22

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
    tzdata openssl unzip nginx bash ca-certificates s6 curl ssmtp mailx php84 php84-phar php84-curl \
    php84-fpm php84-json php84-zlib php84-xml php84-dom php84-ctype php84-opcache php84-zip php84-iconv \
    php84-pdo php84-pdo_mysql php84-pdo_sqlite php84-pdo_pgsql php84-mbstring php84-session php84-bcmath \
    php84-gd php84-openssl php84-sockets php84-posix php84-ldap php84-simplexml php84-xmlwriter && \
    rm -rf /var/www/localhost && \
    rm -f /etc/php84/php-fpm.d/www.conf && \
    ln -sf /usr/bin/php84 /usr/bin/php

ADD . /var/www/app
ADD docker/ /

RUN rm -rf /var/www/app/docker && echo $VERSION > /var/www/app/app/version.txt

HEALTHCHECK --start-period=3s --timeout=5s \
  CMD curl -f http://localhost/healthcheck.php || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD []
