FROM php:fpm-alpine

LABEL maintainer="baicao https://github.com/baicao/ObsidianPlus"

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions yaml

WORKDIR /var/www/changedu/
COPY ./*.php ./
COPY ./*.svg ./
COPY ./*.ico ./
COPY ./.styles/ ./.styles/
COPY ./.js/ ./.js/
COPY ./.src/ ./.src/
COPY ./vendor/ ./vendor/

VOLUME /var/www/changedu/

EXPOSE 9000

# 启动PHP-FPM
CMD ["php-fpm"]