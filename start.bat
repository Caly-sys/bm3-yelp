@echo off
echo Starting Laravel server...
start cmd /k "php artisan serve"

echo Starting Vite development server...
start cmd /k "npm run dev"

echo Both servers started in separate windows!
    