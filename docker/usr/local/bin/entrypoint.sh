#!/bin/bash

chown -R nginx:nginx /var/www/app/data
chown -R nginx:nginx /var/www/app/plugins

exec /bin/s6-svscan /etc/services.d
