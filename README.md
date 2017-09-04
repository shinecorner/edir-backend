
# Installation

### 1.) Environment
```sh
php -r "copy('.env.example', '.env');"
Anschließend die .env Datei anpassen (Mysql)
```

### 2.) Vendor / Database
```sh
composer install --no-dev
php artisan key:generate
php artisan migrate
php  artisan storage:link
```

### 3.) NPM Packages
```sh
npm install
gulp
```

### 4.) Companies Data

#### Before the next step: make sure elasticsearch is installed and running

```sh
php artisan mapping:run
php artisan import:addr      <-- this should sync to elasticsearch with ->save(). no extra import needed.
php artisan generate:counts
// php artisan db:seed <-- only for testing
```

# Optimize Production
```sh
echo "" | sudo -S service php7.1-fpm reload
php artisan cache:clear
php artisan view:clear
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan opcache:clear 
```