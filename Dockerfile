ARG PHP=7.2

FROM php:${PHP}-cli-alpine3.8

ENV PS1="\[\033[01;31m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ "

RUN apk add --no-cache \
        bash \
        bzip2-dev \
        git \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
    && docker-php-ext-install \
        bz2 \
    && pecl install \
        xdebug \
    && docker-php-ext-enable \
        xdebug \
    && apk del .build-deps

WORKDIR /project

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer
