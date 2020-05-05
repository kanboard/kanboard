# This Dockerfile can be used to run unit tests.
# This image is published on the Docker Hub: kanboard/tests:latest
FROM debian:10-slim

RUN apt-get update -y -q && \
    apt-get install -y \
        php-cli \
        php-mbstring \
        php-sqlite3 \
        php-opcache \
        php-json \
        php-ldap \
        php-gd \
        php-zip \
        php-curl \
        php-xml \
        php-mysql \
        php-pgsql \
        composer \
        npm \
        git \
        make \
        mariadb-client \
        postgresql-client
