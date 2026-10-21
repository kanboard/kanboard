FROM alpine:3.24

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
    tzdata openssl unzip nginx bash ca-certificates s6 curl ssmtp mailx php85 php85-phar php85-curl \
    php85-fpm php85-json php85-zlib php85-xml php85-dom php85-ctype php85-zip php85-iconv \
    php85-pdo php85-pdo_mysql php85-pdo_sqlite php85-pdo_pgsql php85-mbstring php85-session php85-bcmath \
    php85-gd php85-openssl php85-sockets php85-posix php85-ldap php85-simplexml php85-xmlwriter && \
    rm -rf /var/www/localhost && \
    rm -f /etc/php85/php-fpm.d/www.conf && \
    ln -sf /usr/bin/php85 /usr/bin/php

ADD . /var/www/app
ADD docker/ /

RUN rm -rf /var/www/app/docker && echo $VERSION > /var/www/app/app/version.txt

HEALTHCHECK --start-period=3s --timeout=5s \
  CMD curl -f http://localhost/healthcheck.php || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD []
