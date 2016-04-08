#!/bin/bash

cd /var/www/laravel
php artisan db:seed --class DatabaseSeeder
/usr/sbin/apache2ctl -D FOREGROUND
