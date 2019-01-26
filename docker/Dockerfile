FROM php:7.2-apache

COPY ./vendor/shurupov/qengine/setup/host.conf /etc/apache2/sites-available

RUN apt-get update && apt-get install --yes --no-install-recommends libssl-dev mc
RUN pecl install mongodb && docker-php-ext-enable mongodb
RUN a2enmod rewrite

ENV user weber

RUN useradd -m -d /home/${user} ${user} && chown -R ${user} /home/${user}
RUN sed -i "s/www-data/${user}/g" /etc/apache2/envvars

RUN rm /etc/apache2/sites-enabled/*
RUN ln -s /etc/apache2/sites-available/host.conf /etc/apache2/sites-enabled/


