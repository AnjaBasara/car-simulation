FROM php:8.2-alpine

RUN apk update

RUN apk add --no-cache \
    bash \
    git \
    curl \
    linux-headers

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN alias composer='php /usr/bin/composer'

RUN apk add --no-cache $PHPIZE_DEPS

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www
COPY . .

COPY ./docker-entrypoint.sh /usr/bin
ENTRYPOINT ["/usr/bin/docker-entrypoint.sh"]
