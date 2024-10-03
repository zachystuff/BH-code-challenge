FROM --platform=linux/amd64 php:8.3.2-fpm

RUN apt-get update && apt-get install -y \
  git \
  libicu-dev \
  libpq-dev \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && docker-php-ext-install -j$(nproc) intl \
  && docker-php-ext-install -j$(nproc) pgsql \
  && docker-php-ext-install -j$(nproc) pdo_pgsql \
  && docker-php-ext-install -j$(nproc) pdo_mysql

WORKDIR /var/www

RUN curl -sS https://get.symfony.com/cli/installer | bash
# make symfony command available globally
# RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 9000
CMD ["php-fpm"]