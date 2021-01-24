FROM drupal:8.8-fpm-alpine
ENV DRUSH_VERSION 8.1.2

RUN apk update
RUN apk add curl wget vim git unzip mysql-client

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    ln -s /root/.composer/vendor/bin/drush /usr/local/bin/drush

# Install Drush
RUN composer global require drush/drush:8.* && \
    composer global update

# Install Redis
#RUN pecl install -o -f redis \
#&&  rm -rf /tmp/pear \
#&&  docker-php-ext-enable redis

# Clean repository
#RUN apt-get clean && rm -rf /var/lib/apt/lists/*