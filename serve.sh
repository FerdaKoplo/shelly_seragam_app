#!/bin/bash

cleanup() {
    echo "Stopping development servers..."
    kill $PID_ARTISAN $PID_NPM
    exit
}

trap cleanup SIGINT

echo "Starting laravel server"
php artisan serve &
PID_ARTISAN=$!

echo "Starting frontend server"
npm run dev &
PID_NPM=$!

wait
