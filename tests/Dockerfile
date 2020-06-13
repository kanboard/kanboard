# This Dockerfile can be used to run unit tests.
# This image is published on the Docker Hub: kanboard/tests:latest
FROM ubuntu:20.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update -y -q && \
    apt-get install -y \
        apache2 \
        libapache2-mod-php \
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
        postgresql-client \
    a2enmod rewrite
