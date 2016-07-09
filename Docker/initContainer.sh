#!/bin/bash

cd /var/www/
php artisan db:seed --class DatabaseSeeder
php artisan db:seed --class EnrichmentAPISeeder
/start.sh
