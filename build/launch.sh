#!/bin/bash

# Write environment variables to script
declare -p | grep -Ev 'BASHOPTS|BASH_VERSINFO|EUID|PPID|SHELLOPTS|UID' > /container.env
chmod 744 /container.env

su-exec app php /var/app/www/bin/uptime_wait

su-exec app php /var/app/www/bin/console migrations:migrate --allow-no-migration

exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
