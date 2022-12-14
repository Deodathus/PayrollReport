FROM php:8.1-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip librabbitmq-dev \
    && pecl install apcu amqp xdebug rabbitmq redis-5.3.7 \
    && docker-php-ext-install intl opcache pdo pdo_mysql sockets zip \
    && docker-php-ext-enable apcu amqp redis \
    && docker-php-ext-configure zip

RUN apt-get update && apt-get install -y supervisor
RUN mkdir -p /var/run/sshd /var/log/supervisor /var/run/supervisor

COPY supervisord.conf /etc/supervisor/supervisord.conf

COPY entrypoint-base.sh /usr/local/bin/entrypoint-base.sh

RUN chmod +x /usr/local/bin/entrypoint-base.sh

COPY xdebug.ini "${PHP_INI_DIR}/conf.d"

WORKDIR /var/www/payroll_report

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash

ENTRYPOINT ["/bin/sh", "/usr/local/bin/entrypoint-base.sh"]