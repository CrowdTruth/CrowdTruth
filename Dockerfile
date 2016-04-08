FROM eboraas/laravel

RUN apt-get -y install mongodb php5-mongo php5-curl php5-mcrypt

ADD Docker/000-laravel.conf /etc/apache2/sites-available/
ADD . /var/www/laravel/
RUN chown www-data:www-data -R /var/www/laravel/

RUN chmod +x /var/www/laravel/Docker/initContainer.sh
CMD "/var/www/laravel/Docker/initContainer.sh"]
