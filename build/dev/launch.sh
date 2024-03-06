#!/bin/bash

# Write environment variables to script
declare -p | grep -Ev 'BASHOPTS|BASH_VERSINFO|EUID|PPID|SHELLOPTS|UID' > /container.env
chmod 744 /container.env

chown -R app:app /var/app/uploads

cd /var/app/www/frontend
su-exec app npm ci
su-exec app npm run build

cd /var/app/www/backend
su-exec app composer install

app_cli init

exec "$@"
