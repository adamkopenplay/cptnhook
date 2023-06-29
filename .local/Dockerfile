FROM php:8.2-cli

RUN apt-get update
RUN apt-get install -y libzip-dev
RUN docker-php-ext-install zip

# Add a default user; not root 
RUN useradd phpcli -s /bin/bash -m -g root

# Install latest version of composer; may need to be restricted to a particular version eventually
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

USER phpcli

RUN mkdir -p /home/phpcli/.composer/cache

WORKDIR /home/phpcli
