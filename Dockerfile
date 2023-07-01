FROM alpine:3.18

LABEL org.opencontainers.image.source https://github.com/kanboard/kanboard
LABEL org.opencontainers.image.title=Kanboard
LABEL org.opencontainers.image.description="Kanboard is project management software that focuses on the Kanban methodology"
LABEL org.opencontainers.image.vendor=Kanboard
LABEL org.opencontainers.image.licenses=MIT
LABEL org.opencontainers.image.url=https://kanboard.org
LABEL org.opencontainers.image.documentation=https://docs.kanboard.org

VOLUME /var/www/app/data
VOLUME /var/www/app/plugins
VOLUME /etc/nginx/ssl

EXPOSE 80 443

ARG VERSION

RUN apk --no-cache --update add \
    tzdata openssl unzip nginx bash ca-certificates s6 curl ssmtp mailx php82 php82-phar php82-curl \
    php82-fpm php82-json php82-zlib php82-xml php82-dom php82-ctype php82-opcache php82-zip php82-iconv \
    php82-pdo php82-pdo_mysql php82-pdo_sqlite php82-pdo_pgsql php82-mbstring php82-session php82-bcmath \
    php82-gd php82-openssl php82-sockets php82-posix php82-ldap php82-simplexml && \
    rm -rf /var/www/localhost && \
    rm -f /etc/php82/php-fpm.d/www.conf && \
    ln -s /usr/bin/php82 /usr/bin/php

ADD . /var/www/app
ADD docker/ /

RUN rm -rf /var/www/app/docker && echo $VERSION > /var/www/app/app/version.txt

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD []
