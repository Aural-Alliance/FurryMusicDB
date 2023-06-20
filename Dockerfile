FROM php:8.2-fpm-alpine3.18

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
COPY ./build/Caddyfile /etc/Caddyfile
COPY ./build/launch.sh /var/app/launch.sh
COPY ./build/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./build/phpfpmpool.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./build/php.ini /usr/local/etc/php/php.ini
COPY ./build/crontab /var/app/crontab

# Set up App user
RUN mkdir -p /var/app/www \
    && addgroup -g 1000 app \
    && adduser -u 1000 -G app -h /var/app/ -s /bin/sh -D app \
    && addgroup app www-data \
    && mkdir -p /var/app/www /var/app/www_tmp /run/supervisord \
    && chown -R app:app /var/app /run/supervisord \
    && chmod a+x /var/app/launch.sh

WORKDIR /var/app/www

COPY --chown=app:app ./ /var/app/www

USER app

RUN composer install --no-dev

USER root

EXPOSE 8000

CMD ["/var/app/launch.sh"]
