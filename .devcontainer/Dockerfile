FROM debian:12

ENV DEBIAN_FRONTEND=noninteractive

RUN apt update \
    && apt install -y \
        libapache2-mod-php8.2 \
        php8.2-cli \
        php8.2-zip \
        php8.2-xml \
        php8.2-pgsql \
        php8.2-mysql \
        php8.2-sqlite3 \
        php8.2-mcrypt \
        php8.2-mbstring \
        php8.2-ldap \
        php8.2-gd \
        php8.2-curl \
        netcat-traditional \
        curl \
        sqlite3 \
        default-mysql-client \
        postgresql-client \
        composer \
        git \
        zsh \
        make \
    && apt clean -y \
    && chsh -s /bin/zsh \
    && sh -c "$(curl -fsSL https://raw.githubusercontent.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"
