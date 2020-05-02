FROM amd64/php:7.3-apache

RUN apt-get update 
RUN apt-get install -y --no-install-recommends libmagickwand-dev 
RUN rm -rf /var/lib/apt/lists/* 
RUN pecl install imagick-3.4.3 
RUN docker-php-ext-enable imagick
RUN  apt-get install -yqq --no-install-recommends libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd  --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd exif

EXPOSE 80

COPY [ "config/apache2.conf", "/etc/apache2/apache2.conf" ]
COPY [ "config/000-default.conf", "/etc/apache2/sites-available/000-default.conf" ]


# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Override with custom opcache settings
COPY config/easyshare.ini $PHP_INI_DIR/conf.d/

RUN a2enmod rewrite 


COPY [ "web/", "/var/www/html/" ]

