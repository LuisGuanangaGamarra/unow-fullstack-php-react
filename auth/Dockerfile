FROM php:8.4-fpm

RUN apt-get update && apt-get install -y wget openssl jq \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    zlib1g-dev \
    && docker-php-ext-install intl pdo_mysql zip \
    && wget https://github.com/vishnubob/wait-for-it/raw/master/wait-for-it.sh \
    && chmod +x wait-for-it.sh \
    && mv wait-for-it.sh /usr/local/bin/wait-for-it


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get install symfony-cli

WORKDIR /var/www/html
COPY . /var/www/html

ENV PASSPHRASE="tu_frase_secreta"
ENV KEY_DIR=./config/jwt
RUN mkdir -p ${KEY_DIR}

RUN openssl genpkey -algorithm RSA -out ${KEY_DIR}/private.key -aes256 -pass pass:${PASSPHRASE} \
    && openssl rsa -pubout -in ${KEY_DIR}/private.key -out ${KEY_DIR}/public.key -passin pass:${PASSPHRASE}

RUN mkdir -p /var/www/html/var /var/www/html/vendor
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor

RUN composer install --optimize-autoloader

EXPOSE 8000