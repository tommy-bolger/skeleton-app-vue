#!/bin/bash

echo "Copying the .env file for development..."
cp .env.example .env

echo "Running composer install..."
docker compose exec wild-alaskan-backend composer install

echo "Generating an application key..."
docker compose exec wild-alaskan-backend php artisan key:generate

echo "Restarting environment..."
docker compose down
docker volume rm wild-alaskan-test_wild-alaskan-mysql
docker compose up -d

sleep 10

echo "Running migrations..."
docker compose exec wild-alaskan-backend php artisan migrate

echo "Running seeders..."
docker compose exec wild-alaskan-backend php artisan db:seed --class=RecipesSeeder

echo "Clearing cache..."
docker compose exec wild-alaskan-backend php artisan cache:clear
docker compose exec wild-alaskan-backend php artisan config:clear
docker compose exec wild-alaskan-backend php artisan route:clear
docker compose exec wild-alaskan-backend php artisan view:clear

echo "Linking storage..."
docker compose exec wild-alaskan-backend php artisan storage:link
