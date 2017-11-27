FROM php:latest

COPY ./src /app/src
COPY composer.json /app

RUN apt-get update -yqq
RUN apt-get install -yqq libcurl4-gnutls-dev
RUN docker-php-ext-install mbstring curl json
RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar install