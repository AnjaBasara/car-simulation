#!/bin/bash

cd /var/www

composer install --no-interaction
cp .env.example .env
php artisan key:generate
