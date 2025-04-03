FROM phusion/baseimage:noble-1.0.1
MAINTAINER Matthew Rayner <hello@rayner.io>
ENV REFRESHED_AT 2025-04-02

# based on dgraziotin/lamp
# MAINTAINER Daniel Graziotin <daniel@ineed.coffee>

ENV DOCKER_USER_ID 501 
ENV DOCKER_USER_GID 20

ENV BOOT2DOCKER_ID 1000
ENV BOOT2DOCKER_GID 50

# Membership Signup Environment variables - START
ENV APP_SMTP_OutboundHost "smtp.googlemail.com"
ENV APP_SMTP_OutboundPort 587
ENV APP_SMTP_OutboundEncrypt "tls"
ENV APP_SMTP_OutboundAuth true
ENV APP_SMTP_OutboundUsername "X@gmail.com"
ENV APP_SMTP_OutboundPassword "XX"
ENV APP_SMTP_OutboundFromAddress "X@gmail.com"
ENV APP_SMTP_OutboundFromName "Membership Signup"

ENV APP_WEBSITE_Name "Local Test"
ENV APP_WEBSITE_RootURL "/"
ENV APP_WEBSITE_Timezone "America/Vancouver"

ENV APP_PDF_OutputPath "output/"
ENV APP_PDF_Membershipfile "forms/MembershipForm2024-2025.pdf"
ENV APP_PDF_MembershipfileEmailViewTemplate "MembershipForm.pdf"
ENV APP_PDF_Waiverfile "forms/E-waiver-FMCBC-Universal-Waiver-Basic-2022.pdf"
ENV APP_PDF_WaiverfileEmailViewTemplate "E-waiver-FMCBC-Universal-Waiver-Basic.pdf"
ENV APP_PDF_WebformEmailField "emailaddress"
ENV APP_PDF_PDFTKPath ""
ENV APP_PDF_EmailPDFToMember true
#comma separated list of people to email a copy of the form
ENV APP_PDF_EmailPDFToDirectors ""
ENV APP_PDF_FormsDateRange "May 1, 2024 - April 30, 2025"

ENV APP_TWOFA_Enabled true
ENV APP_TWOFA_TotpKey "DOCKER23MRL5AUQNK3G"
# 60*45
ENV APP_TWOFA_TotpTimeoutSeconds 2700

# Membership Signup Environment variables - END

# Use baseimage-docker's init system.
CMD ["/sbin/my_init"]

# Tweaks to give Apache/PHP write permissions to the app
#RUN usermod -u ${BOOT2DOCKER_ID} www-data && \
#    usermod -G staff www-data
RUN usermod -G staff www-data

RUN groupmod -g $(($BOOT2DOCKER_GID + 10000)) $(getent group $BOOT2DOCKER_GID | cut -d: -f1)
RUN groupmod -g ${BOOT2DOCKER_GID} staff

# Install packages
ENV DEBIAN_FRONTEND noninteractive
RUN add-apt-repository -y ppa:ondrej/php && \
  apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C && \
  apt-get update && \
  apt-get -y upgrade && \
  apt-get -y install supervisor wget git apache2 php-xdebug libapache2-mod-php pwgen php-apcu php8.4-mcrypt php-gd php-xml php-mbstring zip unzip php-zip curl php-curl pdftk && \
  apt-get -y autoremove && \
  echo "ServerName localhost" >> /etc/apache2/apache2.conf

# needed for phpMyAdmin
#RUN ln -s /etc/php/8.3/mods-available/mcrypt.ini /etc/php/8.3/mods-available/ && phpenmod mcrypt

# Add image configuration and scripts
ADD /docker/supporting_files/start-apache2.sh /start-apache2.sh
ADD /docker/supporting_files/run.sh /run.sh
RUN chmod 755 /*.sh
ADD /docker/supporting_files/supervisord-apache2.conf /etc/supervisor/conf.d/supervisord-apache2.conf

# Set PHP timezones to America/Vancouver
RUN sed -i "s/;date.timezone =/date.timezone = America\/Vancouver/g" /etc/php/8.4/apache2/php.ini
RUN sed -i "s/;date.timezone =/date.timezone = America\/Vancouver/g" /etc/php/8.4/cli/php.ini

# Add Membership-signup scripts
RUN chmod 755 /*.sh

# Add composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

# config to enable .htaccess
ADD /docker/supporting_files/apache_default /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Configure /app folder with Membership-signup app
RUN mkdir -p /app && rm -fr /var/www/html && ln -s /app /var/www/html
ADD /docker/app/ /app

# Remove temp app files
RUN rm -rf /app/temp/cache
RUN rm -rf /app/temp/twig

ADD /*.php /app/
ADD /*.json /app/
ADD /*.lock /app/
ADD /*.xml /app/
ADD /*.lock /app/
ADD /.htaccess /app/
ADD /cache/ /app/cache
ADD /config/ /app/config
ADD /controllers/ /app/controllers
ADD /forms/ /app/forms
ADD /images/ /app/images
ADD /models/ /app/models
ADD /plugins/ /app/plugins
ADD /styles/ /app/styles
ADD /views/ /app/views

ADD /docker/app_config/ /app

# Install php app dependencies
ENV COMPOSER_ALLOW_SUPERUSER 1
# RUN composer update --no-dev --prefer-dist --no-interaction --optimize-autoloader --working-dir app/
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --working-dir app/

#Environment variables to configure php
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M

# Add volumes for the app
#VOLUME  ["/app" ]

EXPOSE 80
CMD ["/run.sh"]
