#!/bin/bash

# Write environment variables to script
declare -p | grep -Ev 'BASHOPTS|BASH_VERSINFO|EUID|PPID|SHELLOPTS|UID' > /container.env
chmod 744 /container.env

cd /var/app/www/frontend
su-exec app npm ci
su-exec app npm run build

cd /var/app/www/backend
su-exec app composer install

su-exec app php /var/app/www/backend/bin/uptime_wait

su-exec app php /var/app/www/backend/bin/console migrations:migrate --allow-no-migration

mkdir -p /etc/supervisor/conf.d

dockerize -template "/etc/Caddyfile.tmpl:/etc/Caddyfile" \
  -template "/etc/supervisor/supervisord.conf.tmpl:/etc/supervisor/conf.d/supervisord.conf"

exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
