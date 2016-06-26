#!/usr/bin/env bash

function wait_schema_creation() {
    curl -s http://app/login > /dev/null
    sleep $1
}

case "$1" in
"config-sqlite")
    cp /configs/config.sqlite.php /var/www/html/config.php
    /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
    ;;
"config-postgres")
    cp /configs/config.postgres.php /var/www/html/config.php
    /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
    ;;
"config-mysql")
    cp /configs/config.mysql.php /var/www/html/config.php
    /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
    ;;
"integration-test-sqlite")
    wait_schema_creation 1
    /var/www/html/vendor/phpunit/phpunit/phpunit -c /var/www/html/tests/integration.sqlite.xml
    ;;
"integration-test-postgres")
    wait_schema_creation 10
    /var/www/html/vendor/phpunit/phpunit/phpunit -c /var/www/html/tests/integration.postgres.xml
    ;;
"integration-test-mysql")
    wait_schema_creation 15
    /var/www/html/vendor/phpunit/phpunit/phpunit -c /var/www/html/tests/integration.mysql.xml
    ;;
esac
