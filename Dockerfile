# Use the official PHP image with Apache
FROM php:8-apache

# Install any additional extensions you might need
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy your application code to the container
COPY test/ /var/www/html/

# Apache is already configured to serve /var/www/html, and CMD is set to start Apache in the base image
