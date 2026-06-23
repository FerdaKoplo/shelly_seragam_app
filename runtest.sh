#!/bin/bash

php artisan test
php artisan migrate:fresh --seed
