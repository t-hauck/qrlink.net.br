# Use the official PHP Apache image for PHP 8.1
FROM php:8.1-apache

ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt update && apt upgrade -y && \
    apt install -y \
        libsqlite3-dev \
        && docker-php-ext-install pdo pdo_mysql pdo_sqlite

# Enable necessary Apache modules
RUN a2enmod rewrite ssl headers

# Copy the Apache virtualhost configuration file from the host into the container
COPY apache-vhost-container.conf /etc/apache2/sites-available/000-default.conf

# Create Apache logs directory and configure log file permissions
RUN mkdir -p /var/log/apache2 && \
    chown -R www-data:www-data /var/log/apache2 && \
    sed -i 's/ErrorLog \${APACHE_LOG_DIR}\/error.log/ErrorLog \/var\/log\/apache2\/error.log/g' /etc/apache2/apache2.conf && \
    sed -i 's/CustomLog \${APACHE_LOG_DIR}\/access.log combined/CustomLog \/var\/log\/apache2\/access.log combined/g' /etc/apache2/apache2.conf

# Configure Apache to enable necessary modules for PHP
RUN sed -i 's/#LoadModule rewrite_module/LoadModule rewrite_module/g' /etc/apache2/apache2.conf

# Use the default PHP production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Disable PHP/Apache version display
RUN echo "ServerTokens ProductOnly" >> /etc/apache2/apache2.conf
RUN echo "ServerSignature Off" >> /etc/apache2/apache2.conf
RUN sed -i 's/expose_php = On/expose_php = Off/' "$PHP_INI_DIR/php.ini"


# Expose ports 80 and 443 for Apache
EXPOSE 80
EXPOSE 443

CMD apache2-foreground
