FROM php:7.2-apache

RUN apt-get update -qq && apt-get install -yqq \
    zlib1g-dev libicu-dev g++ git && \
    a2enmod rewrite

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ARG apacheconfig
ADD $apacheconfig /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/eventsourcing.app