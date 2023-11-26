FROM php:8.1-fpm

RUN apt-get update && apt-get install -y curl build-essential zlib1g-dev g++ git libicu-dev zip libzip-dev zip nano \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip


RUN pecl install xdebug \ && docker-php-ext-enable xdebug

COPY ./php/xdebug.ini "/usr/local/etc/php/conf.d"

WORKDIR /var/www/project

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin

RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.38.0/install.sh | bash
RUN . ~/.nvm/nvm.sh && nvm install node