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

# Install Dockerize
ENV DOCKERIZE_VERSION v0.7.0
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz

# Copy build config files
COPY ./build/Caddyfile.tmpl /etc/Caddyfile.tmpl
COPY ./build/launch.sh /var/app/launch.sh
COPY ./build/supervisord.conf.tmpl /etc/supervisor/supervisord.conf.tmpl
COPY ./build/phpfpmpool.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./build/php.ini /usr/local/etc/php/php.ini
COPY ./build/crontab /var/app/crontab

# Set up App user
RUN mkdir -p /var/app/www/backend /var/app/www/frontend \
    && addgroup -g 1000 app \
    && adduser -u 1000 -G app -h /var/app/ -s /bin/sh -D app \
    && addgroup app www-data \
    && mkdir -p /var/app/www /var/app/www_tmp /run/supervisord \
    && chown -R app:app /var/app /run/supervisord \
    && chmod a+x /var/app/launch.sh

COPY --chown=app:app ./backend /var/app/www/backend
COPY --chown=app:app ./frontend /var/app/www/frontend

USER app

WORKDIR /var/app/www/backend
RUN composer install --no-dev

WORKDIR /var/app/www/frontend
RUN npm ci \
    && npm run build

USER root

EXPOSE 8000

CMD ["/var/app/launch.sh"]
