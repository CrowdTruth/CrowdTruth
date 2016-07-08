#!/bin/bash

cd /var/www/
php artisan db:seed --class DatabaseSeeder
/start.sh
