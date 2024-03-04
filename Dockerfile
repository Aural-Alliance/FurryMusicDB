FROM php:8.3-fpm-alpine3.19 AS base

ENV TZ=UTC

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions @composer gd curl xml zip bcmath mbstring intl pdo_pgsql

RUN apk add --update --no-cache \
    zip git curl bash \
    supervisor \
    caddy \
    su-exec \
    postgresql-client \
    supercronic \
    npm

# Copy build config files
COPY ./build/supervisord.conf /etc/supervisor/supervisord.conf
COPY ./build/services/ /etc/supervisor.d/
COPY ./build/phpfpmpool.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./build/php.ini /usr/local/etc/php/php.ini
COPY ./build/crontab /var/app/crontab
COPY ./build/scripts/ /usr/local/bin

RUN chmod a+rx /usr/local/bin

# Set up App user
RUN mkdir -p /var/app/www/backend /var/app/www/frontend \
    && addgroup -g 1000 app \
    && adduser -u 1000 -G app -h /var/app/ -s /bin/sh -D app \
    && addgroup app www-data \
    && mkdir -p /var/app/www /var/app/uploads /var/app/www_tmp /run/supervisord \
    && chown -R app:app /var/app /run/supervisord \
    && chmod a+rx /var/app/launch.sh

USER root

EXPOSE 8000

ENTRYPOINT ["/var/app/launch.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]

FROM base AS development

COPY ./build/dev/Caddyfile /etc/Caddyfile
COPY ./build/dev/services/ /etc/supervisor.d/
COPY ./build/dev/launch.sh /var/app/launch.sh

FROM base AS production

COPY ./build/prod/Caddyfile /etc/Caddyfile
COPY ./build/prod/launch.sh /var/app/launch.sh

COPY --chown=app:app ./backend /var/app/www/backend
COPY --chown=app:app ./frontend /var/app/www/frontend
